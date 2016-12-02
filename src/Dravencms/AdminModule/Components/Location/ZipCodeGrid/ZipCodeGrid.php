<?php

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\ZipCodeGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
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
     * @return \Grido\Grid
     */
    protected function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->zipCodeRepository->getZipCodeQueryBuilder());

        $renderer = function ($row) {
            return $row->usersCity->name;
        };
        $grid->addColumnText('city.name', 'Město')
            ->setSortable()
            ->setColumn('city.name')
            ->setCustomRenderExport($renderer)
            ->setFilterText()
            ->setSuggestion($renderer);

        $grid->addColumnText('name', 'PSČ')
            ->setSortable()
            ->setFilterText()
            ->setSuggestion();

        if ($this->presenter->isAllowed('location', 'zipCodeEdit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('location', 'zipCodeDelete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setDisable(function ($row) {
                    return (count($row->getStreets()) > 0);
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat PSČ %s ?', $row->getName()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridZipOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i PSČ ?');
        }
        $grid->setExport();

        return $grid;
    }


    /**
     * @param $action
     * @param $ids
     */
    public function gridZipOperationsHandler($action, $ids)
    {
        switch ($action) {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
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