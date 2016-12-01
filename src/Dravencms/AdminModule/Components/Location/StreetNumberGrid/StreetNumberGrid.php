<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\StreetNumberGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
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
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->streetNumberRepository->getStreetNumberQueryBuilder());

        $grid->addColumnText('name', 'Street Number')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnText('street.name', 'Street')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnText('street.zipCode.name', 'ZIP')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnText('street.zipCode.city.name', 'City')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnText('street.zipCode.city.country.name', 'Country')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        if ($this->presenter->isAllowed('user', 'streetEdit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('user', 'streetDelete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat cislo ulice %s ?', $row->getName()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i zákazníků ?');
        }
        $grid->setExport();

        return $grid;
    }


    /**
     * @param $action
     * @param $ids
     */
    public function gridOperationsHandler($action, $ids)
    {
        switch ($action) {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
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