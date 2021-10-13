<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\LocationModule;

use Dravencms\AdminModule\Components\Location\RegionForm\RegionForm;
use Dravencms\AdminModule\Components\Location\RegionForm\RegionFormFactory;
use Dravencms\AdminModule\Components\Location\RegionGrid\RegionGrid;
use Dravencms\AdminModule\Components\Location\RegionGrid\RegionGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Flash;
use Dravencms\Database\EntityManager;
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
     */
    public function actionDefault(): void
    {
        $this->template->h1 = $this->translator->translate('Regions');
    }

    /**
     * @isAllowed(location, regionEdit)
     * @param int|null $id
     */
    public function actionEdit(int $id = null): void
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
    public function createComponentRegionForm(): RegionForm
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
    protected function createComponentRegionGrid(): RegionGrid
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
