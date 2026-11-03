<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\CountryGrid;


use Dravencms\User\Attributes\IsAllowed;
use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Model\Location\Repository\CountryRepository;
use Dravencms\Database\EntityManager;
use Nette\Security\User;
use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;

class CountryGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var CountryRepository */
    private $countryRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var User */
    private $user;

    /** @var array */
    public $onDelete = [];

    /**
     * CountryGrid constructor.
     * @param CountryRepository $countryRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param User $user
     */
    public function __construct(CountryRepository $countryRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, User $user)
    {
        $this->baseGridFactory = $baseGridFactory;
        $this->countryRepository = $countryRepository;
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
        $grid->setWritePermission('location', 'countryEdit');

        $grid->setDataSource($this->countryRepository->getCountryQueryBuilder());

        $grid->addColumnText('name', 'location.admin.field.country')
            ->setSortable()
            ->setFilterText();

        if ($this->user->isAllowed('location', 'countryEdit'))
        {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('location.admin.action.edit')
                ->setClass('btn btn-sm btn-primary');
        }

        if ($this->user->isAllowed('location', 'countryDelete'))
        {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('location.admin.action.delete')
                ->setClass('btn btn-sm btn-danger ajax')
                ->setConfirmation(new StringConfirmation('location.admin.confirm.delete', 'name'));

            $grid->allowRowsAction('delete', function($item) {
                return (count($item->getCities()) == 0);
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
    #[\Dravencms\User\Attributes\IsAllowed('location', 'countryDelete')]
    #[\Dravencms\Security\Mutation]
    public function handleDelete($id): void
    {
        $this->requirePermission('location', 'countryDelete');
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