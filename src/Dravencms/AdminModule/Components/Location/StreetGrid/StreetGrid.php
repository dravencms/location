<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\StreetGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Model\Location\Repository\StreetRepository;
use Kdyby\Doctrine\EntityManager;

class StreetGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var StreetRepository */
    private $streetRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var array */
    public $onDelete = [];

    /**
     * StreetGrid constructor.
     * @param StreetRepository $streetRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     */
    public function __construct(StreetRepository $streetRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->streetRepository = $streetRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Grido\Grid
     */
    protected function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->streetRepository->getStreetQueryBuilder());

        $grid->addColumnText('name', 'Street')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnText('zipCode.name', 'ZIP')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnText('zipCode.city.name', 'City')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        if ($this->presenter->isAllowed('location', 'streetEdit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('location', 'streetDelete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setDisable(function ($row) {
                    return (count($row->getStreetNumbers()) > 0);
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat ulici %s ?', $row->getName()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridStreetOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i zákazníků ?');
        }
        $grid->setExport();

        return $grid;
    }


    /**
     * @param $action
     * @param $ids
     */
    public function gridStreetOperationsHandler($action, $ids)
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
        $streets = $this->streetRepository->getById($id);
        foreach ($streets AS $street) {
            $this->entityManager->remove($street);
        }
        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/StreetGrid.latte');
        $template->render();
    }
}