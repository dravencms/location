<?php

/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Dravencms\AdminModule\Components\Location\RegionGrid;

use Dravencms\Model\Location\Entities\Region;
use Dravencms\Model\Location\Repository\RegionRepository;
use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Kdyby\Doctrine\EntityManager;

/**
 * Description of CompanyGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class RegionGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var RegionRepository */
    private $regionRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var Region|null */
    private $parentRegion = null;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * RegionGrid constructor.
     * @param RegionRepository $regionRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param Region|null $parentRegion
     */
    public function __construct(RegionRepository $regionRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, Region $parentRegion = null)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->regionRepository = $regionRepository;
        $this->parentRegion = $parentRegion;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Grido\Grid
     */
    protected function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->regionRepository->getRegionItemsQueryBuilder($this->parentRegion));

        $grid->addColumnText('name', 'Name')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnBoolean('isActive', 'Active');

        $grid->addColumnText('position', 'Position')
            ->setCustomRender(function($row){
                $elDown = Html::el('a');
                $elDown->class = "btn btn-xs";
                $elDown->href($this->link('down!', ['id' => $row->getId()]));
                $elDown->setHtml('<i class="fa fa-chevron-down" aria-hidden="true"></i>');

                $elUp = Html::el('a');
                $elUp->class = "btn btn-xs";
                $elUp->href($this->link('up!', ['id' => $row->getId()]));
                $elUp->setHtml('<i class="fa fa-chevron-up" aria-hidden="true"></i>');
                return $elUp.$elDown;
            });

        $header = $grid->getColumn('position')->headerPrototype;
        $header->style['width'] ='2%';
        $header->class[] = 'center';
        $grid->getColumn('position')->cellPrototype->class[] = 'center';

        if ($this->presenter->isAllowed('czechDatabase', 'regionEdit'))
        {
            $grid->addActionHref('subcategory', 'Subcategory items')
                ->setIcon('folder-open')
                ->setCustomHref(function ($item) {
                    return $this->presenter->link('Category:default', ['categoryId' => $item->getId()]);
                });

            $grid->addActionHref('edit', 'Edit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('czechDatabase', 'regionDelete')) {

            $grid->addActionHref('delete', 'Delete', 'delete!')
                ->setCustomHref(function ($row) {
                    return $this->link('delete!', $row->getId());
                })
                ->setDisable(function($row){
                    $bool = ($row->getCities()->count());
                    return $bool;
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($item) {
                    return ["Are you sure you want to delete %s ?", $item->getName()];
                });
        }

        $operations = ['delete' => 'Delete'];
        $grid->setOperation($operations, [$this, 'gridOperationsHandler'])
            ->setConfirm('delete', 'Are you sure you want to delete %i items?');

        return $grid;
    }


    /**
     * @param $action
     * @param $ids
     */
    public function gridOperationsHandler($action, $ids)
    {
        switch ($action)
        {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function handleDelete($id)
    {
        $categories = $this->regionRepository->getById($id);
        foreach ($categories AS $category)
        {
            $this->entityManager->remove($category);
        }

        $this->entityManager->flush();

        $this->onDelete($this->parentRegion);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function handleUp($id)
    {
        $menuItem = $this->regionRepository->getOneById($id);
        $this->regionRepository->moveUp($menuItem, 1);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function handleDown($id)
    {
        $menuItem = $this->regionRepository->getOneById($id);
        $this->regionRepository->moveDown($menuItem, 1);
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/RegionGrid.latte');
        $template->render();
    }
}
