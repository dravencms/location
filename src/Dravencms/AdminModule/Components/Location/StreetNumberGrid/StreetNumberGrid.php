<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\StreetNumberGrid;


use Dravencms\User\Attributes\IsAllowed;
use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Model\Location\Repository\StreetNumberRepository;
use Dravencms\Database\EntityManager;
use Nette\Security\User;
use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;

class StreetNumberGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var StreetNumberRepository */
    private $streetNumberRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var User */
    private $user;

    /** @var array */
    public $onDelete = [];

    /**
     * StreetGrid constructor.
     * @param StreetNumberRepository $streetNumberRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     */
    public function __construct(StreetNumberRepository $streetNumberRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, User $user)
    {
        $this->baseGridFactory = $baseGridFactory;
        $this->streetNumberRepository = $streetNumberRepository;
        $this->entityManager = $entityManager;
        $this->user = $user;
    }


    /**
     * @param string $name
     * @return Grid
     * @throws \Contributte\Datagrid\Exception\DatagridException
     */
    protected function createComponentGrid(string $name): Grid
    {
        /** @var Grid $grid */
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setDataSource($this->streetNumberRepository->getStreetNumberQueryBuilder());

        $grid->addColumnText('name', 'location.admin.field.streetNumber')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('streetName', 'location.admin.field.street', 'street.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('streetZipCodeName', 'location.admin.field.zipCode', 'street.zipCode.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('streetZipCodeCityName', 'location.admin.field.city', 'street.zipCode.city.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('streetZipCodeCityCountryName', 'location.admin.field.country', 'street.zipCode.city.country.name')
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
    #[IsAllowed('user', 'streetDelete')]
    public function handleDelete($id): void
    {
        $streetNumbers = $this->streetNumberRepository->getById($id);
        foreach ($streetNumbers AS $streetNumber) {
            $this->entityManager->remove($streetNumber);
        }
        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/StreetNumberGrid.latte');
        $template->render();
    }
}