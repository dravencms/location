<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\StreetGrid;


use Dravencms\User\Attributes\IsAllowed;
use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Model\Location\Repository\StreetRepository;
use Dravencms\Database\EntityManager;
use Nette\Security\User;
use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;

class StreetGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var StreetRepository */
    private $streetRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var User */
    private $user;

    /** @var array */
    public $onDelete = [];

    /**
     * StreetGrid constructor.
     * @param StreetRepository $streetRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param User $user
     */
    public function __construct(StreetRepository $streetRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, User $user)
    {
        $this->baseGridFactory = $baseGridFactory;
        $this->streetRepository = $streetRepository;
        $this->entityManager = $entityManager;
        $this->user = $user;
    }


    /**
     * @param $name
     * @return Grid
     * @throws \Contributte\Datagrid\Exception\DatagridException
     */
    protected function createComponentGrid($name): Grid
    {
        /** @var Grid $grid */
        $grid = $this->baseGridFactory->create($this, $name);
        $grid->setWritePermission('location', 'streetEdit');

        $grid->setDataSource($this->streetRepository->getStreetQueryBuilder());

        $grid->addColumnText('name', 'location.admin.field.street')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('zipCodeName', 'location.admin.field.zipCode', 'zipCode.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('zipCodeCityName', 'location.admin.field.city', 'zipCode.city.name')
            ->setSortable()
            ->setFilterText();

        if ($this->user->isAllowed('location', 'streetEdit'))
        {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('location.admin.action.edit')
                ->setClass('btn btn-sm btn-primary');
        }

        if ($this->user->isAllowed('location', 'streetDelete'))
        {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('location.admin.action.delete')
                ->setClass('btn btn-sm btn-danger ajax')
                ->setConfirmation(new StringConfirmation('location.admin.confirm.delete', 'name'));

            $grid->allowRowsAction('delete', function($item) {
                return (count($item->getStreetNumbers()) == 0);
            });

            $grid->addGroupAction('location.admin.action.delete')->onSelect[] = [$this, 'gridGroupActionDelete'];
        }

        $grid->addExportCsvFiltered('location.admin.action.exportFiltered', 'acl_resource_filtered.csv')
            ->setTitle('location.admin.action.exportFiltered');

        $grid->addExportCsv('location.admin.action.exportAll', 'acl_resource_all.csv')
            ->setTitle('location.admin.action.exportAll');

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
     */
    #[\Dravencms\User\Attributes\IsAllowed('location', 'streetDelete')]
    #[\Dravencms\Security\Mutation]
    public function handleDelete($id): void
    {
        $this->requirePermission('location', 'streetDelete');
        $streets = $this->streetRepository->getById($id);
        foreach ($streets AS $street) {
            $this->entityManager->remove($street);
        }
        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/StreetGrid.latte');
        $template->render();
    }
}