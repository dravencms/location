<?php

namespace Dravencms\AdminModule\LocationModule;

use Dravencms\AdminModule\Components\Location\RegionForm\RegionFormFactory;
use Dravencms\AdminModule\Components\Location\RegionGrid\RegionGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Flash;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Dravencms\Model\Location\Repository\RegionRepository;
use Dravencms\Model\Location\Entities\Region;

/**
 * RegionPresenter presenter.
 */
class RegionPresenter extends SecuredPresenter
{
    /** @var RegionRepository @inject */
    public $regionRepository;

    /** @var EntityManager @inject */
    public $entityManager;

    /** @var RegionGridFactory @inject */
    public $regionGridFactory;

    /** @var RegionFormFactory @inject */
    public $regionFormFactory;
    
    /** @var Region|null */
    private $region = null;

    /**
     * @isAllowed(location, regionEdit)
     * @throws Nette\Application\BadRequestException
     */
    public function actionDefault()
    {
        $this->template->h1 = $this->translator->translate('Regions');
    }

    /**
     * @isAllowed(location, regionEdit)
     * @param null $id
     * @throws Nette\Application\BadRequestException
     */
    public function actionEdit($id = null)
    {
        $this->template->h1 = $this->translator->translate('Region');

        if ($id) {
            /** @var Region $category */
            $region = $this->regionRepository->getOneById($id);
            if (!$region) {
                $this->error();
            }

            $this->region = $region;
            $this->template->h1 .= ' - ' . $region->getName();

        } else {
            $this->template->h1 .= ' - ' . $this->translator->translate('New region');

        }
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\RegionForm\RegionForm
     */
    public function createComponentRegionForm()
    {
        $component = $this->regionFormFactory->create($this->region);
        $component->onSuccess[] = function ($region) {

            /** @var Region $region */
            if ($this->region) {
                $this->flashMessage('Changes has been saved.', 'alert-success');
                $this->redirect('Region:edit', ['id' => $region->getId()]);
            } else {
                $this->flashMessage('New region item has been saved.', 'alert-success');
                $this->redirect('Region:');
            }
        };

        return $component;
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\RegionGrid\RegionGrid
     */
    protected function createComponentRegionGrid()
    {
        $control = $this->regionGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Region has been successfully deleted', Flash::SUCCESS);
            $this->redirect('Region:');
        };
        return $control;
    }
}
