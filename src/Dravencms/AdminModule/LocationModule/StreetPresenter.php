<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\LocationModule;


use Dravencms\AdminModule\Components\Location\StreetForm\StreetFormFactory;
use Dravencms\AdminModule\Components\Location\StreetGrid\StreetGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Location\Entities\Street;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\StreetRepository;

/**
 * Description of StreetPresenter
 *
 * @author Adam Schubert
 */
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

    /**
     * @isAllowed(user,streetEdit)
     */
    public function actionDefault()
    {
        $this->template->h1 = 'Přehled ulic';
    }

    /**
     * @param integer $id
     * @isAllowed(user,streetEdit)
     * @throws \Exception
     */
    public function actionEdit($id)
    {
        if ($id) {
            $street = $this->userStreetNumberRepository->getOneById($id);
            if (!$street) {
                $this->error();
            }
            $this->streetFormEntity = $street;
            $this->template->h1 = sprintf('Ulice „%s“', $street->getName());
        } else {
            $this->template->h1 = 'Vytvoření nové ulice';
        }
    }

    /**
     * @return \AdminModule\Components\User\StreetGrid
     */
    public function createComponentGridStreet()
    {
        $control = $this->userStreetGridFactory->create();
        $control->onDelete[] = function(){
            $this->flashMessage('Street has been successfully deleted', 'alert-success');
            $this->redirect('Street:');
        };
        return $control;
    }

    /**
     * @return \AdminModule\Components\User\StreetForm
     */
    public function createComponentFormStreet()
    {
        $component = $this->userStreetFormFactory->create($this->streetFormEntity);
        $component->onSuccess[] = function()
        {
            $this->flashMessage('Ulice byla úspěšně uložena', 'alert-success');
            $this->redirect('Street:');
        };
        return $component;
    }
}
