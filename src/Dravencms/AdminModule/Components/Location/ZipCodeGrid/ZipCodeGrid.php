<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */


namespace Dravencms\AdminModule\Components\Location\ZipCodeGrid;


use Dravencms\User\Attributes\IsAllowed;
use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Model\Location\Repository\ZipCodeRepository;
use Dravencms\Database\EntityManager;
use Nette\Security\User;
use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;

class ZipCodeGrid extends BaseControl
{
    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var ZipCodeRepository */
    private $zipCodeRepository;

    /** @var EntityManager  */
    private $entityManager;

    /** @var User */
    private $user;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * ZipCodeGrid constructor.
     * @param ZipCodeRepository $zipCodeRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param User $user
     */
    public function __construct(ZipCodeRepository $zipCodeRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, User $user)
    {
        $this->baseGridFactory = $baseGridFactory;
        $this->zipCodeRepository = $zipCodeRepository;
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
        $grid->setWritePermission('location', 'zipCodeEdit');

        $grid->setDataSource($this->zipCodeRepository->getZipCodeQueryBuilder());

        $grid->addColumnText('cityName', 'location.admin.field.city', 'city.name')
            ->setSortable()
            ->setFilterText();

        $grid->addColumnText('name', 'location.admin.field.zipCode')
            ->setSortable()
            ->setFilterText();

        if ($this->user->isAllowed('location', 'zipCodeEdit'))
        {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('location.admin.action.edit')
                ->setClass('btn btn-sm btn-primary');
        }

        if ($this->user->isAllowed('location', 'zipCodeDelete'))
        {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('location.admin.action.delete')
                ->setClass('btn btn-sm btn-danger ajax')
                ->setConfirmation(new StringConfirmation('location.admin.confirm.delete', 'name'));

            $grid->allowRowsAction('delete', function($item) {
                return (count($item->getStreets()) == 0);
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
    #[\Dravencms\User\Attributes\IsAllowed('location', 'zipCodeDelete')]
    #[\Dravencms\Security\Mutation]
    public function handleDelete($id): void
    {
        $this->requirePermission('location', 'zipCodeDelete');
        $zipCodes = $this->zipCodeRepository->getById($id);
        foreach($zipCodes AS $zipCode)
        {
            $this->entityManager->remove($zipCode);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/ZipCodeGrid.latte');
        $template->render();
    }
}