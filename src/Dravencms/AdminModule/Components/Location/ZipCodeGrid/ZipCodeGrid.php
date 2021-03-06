<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\ZipCodeGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Model\Location\Repository\ZipCodeRepository;
use Kdyby\Doctrine\EntityManager;

class ZipCodeGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var ZipCodeRepository */
    private $zipCodeRepository;

    /** @var EntityManager  */
    private $entityManager;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * MenuGrid constructor.
     * @param ZipCodeRepository $zipCodeRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     */
    public function __construct(ZipCodeRepository $zipCodeRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->zipCodeRepository = $zipCodeRepository;
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

        $grid->setDataSource($this->zipCodeRepository->getZipCodeQueryBuilder());

        $grid->addColumnText('cityName', 'Město', 'city.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('name', 'PSČ')
            ->setSortable()
            ->setFilterText();

        if ($this->presenter->isAllowed('location', 'zipCodeEdit'))
        {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('Upravit')
                ->setClass('btn btn-xs btn-primary');
        }

        if ($this->presenter->isAllowed('location', 'zipCodeDelete'))
        {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('Smazat')
                ->setClass('btn btn-xs btn-danger ajax')
                ->setConfirm('Do you really want to delete row %s?', 'name');

            $grid->allowRowsAction('delete', function($item) {
                return (count($item->getStreets()) == 0);
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
     * @param integer|array $id
     * @isAllowed(user,zipCodeDelete)
     */
    public function handleDelete($id)
    {
        $zipCodes = $this->zipCodeRepository->getById($id);
        foreach($zipCodes AS $zipCode)
        {
            $this->entityManager->remove($zipCode);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/ZipCodeGrid.latte');
        $template->render();
    }
}