<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\CountryGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Model\Location\Entities\Country;
use Dravencms\Model\Location\Repository\CountryRepository;
use Kdyby\Doctrine\EntityManager;

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
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->countryRepository = $countryRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Grido\Grid
     */
    protected function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->countryRepository->getCountryQueryBuilder());

        $grid->addColumnText('name', 'Country')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        if ($this->presenter->isAllowed('user', 'countryEdit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('user', 'countryDelete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setDisable(function ($row) {
                    /** @var Country $row */
                    return (count($row->getCities()) > 0);
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat Stat %s ?', $row->getName()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridCountryOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i Statu ?');
        }
        $grid->setExport();

        return $grid;
    }


    /**
     * @param $action
     * @param $ids
     */
    public function gridCountryOperationsHandler($action, $ids)
    {
        switch ($action) {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
    }

    /**
     * @param integer|array $id
     * @isAllowed(user,countryDelete)
     */
    public function handleDelete($id)
    {
        $countries = $this->countryRepository->getById($id);
        foreach($countries AS $country)
        {
            $this->entityManager->remove($country);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }


    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/CountryGrid.latte');
        $template->render();
    }
}