<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\CountryGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Model\Location\Repository\CountryRepository;
use Dravencms\Database\EntityManager;

class CountryGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var CountryRepository */
    private $countryRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var array */
    public $onDelete = [];

    /**
     * CountryGrid constructor.
     * @param CountryRepository $countryRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     */
    public function __construct(CountryRepository $countryRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager)
    {
        $this->baseGridFactory = $baseGridFactory;
        $this->countryRepository = $countryRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param string $name
     * @return Grid
     * @throws \Ublaboo\DataGrid\Exception\DataGridException
     */
    protected function createComponentGrid(string $name): Grid
    {
        /** @var Grid $grid */
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setDataSource($this->countryRepository->getCountryQueryBuilder());

        $grid->addColumnText('name', 'Country')
            ->setSortable()
            ->setFilterText();

        if ($this->presenter->isAllowed('location', 'countryEdit'))
        {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('Upravit')
                ->setClass('btn btn-xs btn-primary');
        }

        if ($this->presenter->isAllowed('location', 'countryDelete'))
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
    public function gridGroupActionDelete(array $ids): void
    {
        $this->handleDelete($ids);
    }

    /**
     * @param integer|array $id
     * @isAllowed(user,countryDelete)
     */
    public function handleDelete($id): void
    {
        $countries = $this->countryRepository->getById($id);
        foreach($countries AS $country)
        {
            $this->entityManager->remove($country);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }


    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/CountryGrid.latte');
        $template->render();
    }
}