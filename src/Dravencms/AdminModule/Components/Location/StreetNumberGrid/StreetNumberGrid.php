<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\StreetNumberGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Model\Location\Repository\StreetNumberRepository;
use Kdyby\Doctrine\EntityManager;

class StreetNumberGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var StreetNumberRepository */
    private $streetNumberRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var array */
    public $onDelete = [];

    /**
     * StreetGrid constructor.
     * @param StreetNumberRepository $streetNumberRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     */
    public function __construct(StreetNumberRepository $streetNumberRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->streetNumberRepository = $streetNumberRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Grido\Grid
     */
    protected function createComponentGrid($name)
    {
        /** @var Grid $grid */
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setDataSource($this->streetNumberRepository->getStreetNumberQueryBuilder());

        $grid->addColumnText('name', 'Street Number')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('streetName', 'Street', 'street.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('streetZipCodeName', 'ZIP', 'street.zipCode.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('streetZipCodeCityName', 'City', 'street.zipCode.city.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('streetZipCodeCityCountryName', 'Country', 'street.zipCode.city.country.name')
            ->setSortable()
            ->setFilterText();

        if ($this->presenter->isAllowed('location', 'streetEdit'))
        {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('Upravit')
                ->setClass('btn btn-xs btn-primary');
        }

        if ($this->presenter->isAllowed('location', 'streetDelete'))
        {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('Smazat')
                ->setClass('btn btn-xs btn-danger ajax')
                ->setConfirm('Do you really want to delete row %s?', 'name');

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
     * @param integer|array $id
     * @isAllowed(user,streetDelete)
     */
    public function handleDelete($id)
    {
        $streetNumbers = $this->streetNumberRepository->getById($id);
        foreach ($streetNumbers AS $streetNumber) {
            $this->entityManager->remove($streetNumber);
        }
        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/StreetNumberGrid.latte');
        $template->render();
    }
}