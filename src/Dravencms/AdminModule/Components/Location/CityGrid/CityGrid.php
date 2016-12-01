<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\CityGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Model\Location\Repository\CityRepository;
use Kdyby\Doctrine\EntityManager;

class CityGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var CityRepository */
    private $cityRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var array */
    public $onDelete = [];

    /**
     * CountryGrid constructor.
     * @param CityRepository $cityRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     */
    public function __construct(CityRepository $cityRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->cityRepository = $cityRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Grido\Grid
     */
    protected function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->cityRepository->getCityQueryBuilder());

        $grid->addColumnText('name', 'Město')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnText('country.name', 'Country')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        if ($this->presenter->isAllowed('user', 'cityEdit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('user', 'cityDelete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setDisable(function ($row) {
                    return (count($row->getZipCodes()) > 0);
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat město %s ?', $row->getName()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridCityOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i měst ?');
        }
        $grid->setExport();

        return $grid;
    }


    /**
     * @param $action
     * @param $ids
     */
    public function gridCityOperationsHandler($action, $ids)
    {
        switch ($action) {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
    }

    /**
     * @param integer|array $id
     * @isAllowed(user, cityDelete)
     */
    public function handleDelete($id)
    {
        $cities = $this->cityRepository->getById($id);
        foreach ($cities AS $city) {
            $this->entityManager->remove($city);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }


    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/CityGrid.latte');
        $template->render();
    }
}