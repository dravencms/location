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

use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Model\Location\Entities\Region;
use Dravencms\Model\Location\Repository\RegionRepository;
use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\Html;

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
    
    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * RegionGrid constructor.
     * @param RegionRepository $regionRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     */
    public function __construct(RegionRepository $regionRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->regionRepository = $regionRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return Grid
     */
    protected function createComponentGrid($name)
    {
        /** @var Grid $grid */
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setDataSource($this->regionRepository->getRegionQueryBuilder());

        $grid->addColumnText('name', 'Name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnBoolean('isActive', 'Active');

        $grid->addColumnPosition('position', 'Position');

        if ($this->presenter->isAllowed('location', 'regionEdit'))
        {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('Upravit')
                ->setClass('btn btn-xs btn-primary');
        }

        if ($this->presenter->isAllowed('location', 'regionDelete'))
        {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('Smazat')
                ->setClass('btn btn-xs btn-danger ajax')
                ->setConfirm('Do you really want to delete row %s?', 'name');

            $grid->allowRowsAction('delete', function($item) {
                return (count($item->getCities()) == 0);
            });

            $grid->addGroupAction('Smazat')->onSelect[] = [$this, 'gridGroupActionDelete'];
        }

        $grid->addExportCsvFiltered('Csv export (filtered)', 'acl_resource_filtered.csv')
            ->setTitle('Csv export (filtered)');

        $grid->addExportCsv('Csv export', 'acl_resource_all.csv')
            ->setTitle('Csv export');

        return $grid;
    }

    /**
     * @param array $ids
     */
    public function gridGroupActionDelete(array $ids)
    {
        $this->handleDelete($ids);
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

        $this->onDelete();
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
