<?php declare(strict_types = 1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\LocationModule;



use Dravencms\User\Attributes\IsAllowed;
use Dravencms\AdminModule\Components\Location\StreetForm\StreetForm;
use Dravencms\AdminModule\Components\Location\StreetForm\StreetFormFactory;
use Dravencms\AdminModule\Components\Location\StreetGrid\StreetGrid;
use Dravencms\AdminModule\Components\Location\StreetGrid\StreetGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Flash;
use Dravencms\Model\Location\Entities\Street;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\StreetRepository;

/**
 * Description of StreetPresenter
 *
 * @author Adam Schubert
 */
#[\Dravencms\User\Attributes\IsAllowed('location', 'streetEdit')]
class StreetPresenter extends SecuredPresenter
{
    /** @var StreetRepository @inject */
    public $userStreetNumberRepository;

    /** @var CityRepository @inject */
    public $userCityRepository;

    /** @var StreetGridFactory @inject */
    public $userStreetGridFactory;

    /** @var StreetFormFactory @inject */
    public $userStreetFormFactory;
    
    /** @var Street|null */
    private $streetFormEntity = null;

    #[IsAllowed('location', 'streetEdit')]
    public function actionDefault(): void
    {
        $this->template->h1 = $this->translator->translate('location.admin.title.streets');
    }

    /**
     * @param integer|null $id
     */
    /**
     * @throws \Exception
     */
    #[IsAllowed('location', 'streetEdit')]
    public function actionEdit(?int $id = null): void
    {
        if ($id) {
            $street = $this->userStreetNumberRepository->getOneById($id);
            if (!$street) {
                $this->error();
            }
            $this->streetFormEntity = $street;
            $this->template->h1 = $this->translator->translate('location.admin.title.editStreet', ['name' => $street->getName()]);
        } else {
            $this->template->h1 = $this->translator->translate('location.admin.title.newStreet');
        }
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\StreetGrid\StreetGrid
     */
    public function createComponentGridStreet(): StreetGrid
    {
        $control = $this->userStreetGridFactory->create();
        $control->onDelete[] = function(){
            $this->flashMessage($this->translator->translate('location.admin.flash.streetDeleted'), Flash::SUCCESS);
            $this->redirect('Street:');
        };
        return $control;
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\StreetForm\StreetForm
     */
    public function createComponentFormStreet(): StreetForm
    {
        $component = $this->userStreetFormFactory->create($this->streetFormEntity);
        $component->onSuccess[] = function()
        {
            $this->flashMessage($this->translator->translate('location.admin.flash.streetSaved'), Flash::SUCCESS);
            $this->redirect('Street:');
        };
        return $component;
    }
}
