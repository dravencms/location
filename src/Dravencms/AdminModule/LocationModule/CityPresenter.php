<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\LocationModule;


use Dravencms\AdminModule\Components\Location\CityForm\CityFormFactory;
use Dravencms\AdminModule\Components\Location\CityGrid\CityGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Location\Entities\City;
use Dravencms\Model\Location\Repository\CityRepository;

/**
 * Description of CityPresenter
 *
 * @author Adam Schubert
 */
class CityPresenter extends SecuredPresenter
{
    /** @var CityRepository @inject */
    public $userCityRepository;

    /** @var CityFormFactory @inject */
    public $userCityFormFactory;

    /** @var CityGridFactory @inject */
    public $userCityGridFactory;

    /** @var City|null */
    private $userCityFormEntity = null;
    
    /**
     * @isAllowed(location, cityEdit)
     */
    public function actionDefault()
    {
        $this->template->h1 = 'Přehled měst';
    }

    /**
     * @param  integer $id
     * @isAllowed(location, cityEdit)
     * @throws \Exception
     */
    public function actionEdit($id)
    {
        if ($id) {
            $city = $this->userCityRepository->getOneById($id);
            if (!$city) {
                $this->error();
            }

            $this->userCityFormEntity = $city;
            $this->template->h1 = sprintf('Město „%s“', $city->getName());
        } else {
            $this->template->h1 = 'Založení nového města';
        }
    }

    /**
     * @return \AdminModule\Components\User\CityGrid
     */
    public function createComponentGridCity()
    {
        $control = $this->userCityGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('City has been successfuly deleted', 'alert-success');
            $this->redirect('City:');
        };
        return $control;
    }

    public function createComponentFormCity()
    {
        $control = $this->userCityFormFactory->create($this->userCityFormEntity);
        $control->onSuccess[] = function()
        {
            $this->flashMessage('City has been successfuly saved', 'alert-success');
            $this->redirect('City:');
        };
        return $control;
    }
}
