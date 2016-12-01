<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\LocationModule;


use Dravencms\AdminModule\Components\Location\CountryForm\CountryFormFactory;
use Dravencms\AdminModule\Components\Location\CountryGrid\CountryGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Location\Entities\Country;
use Dravencms\Model\Location\Repository\CountryRepository;

/**
 * Description of CountryPresenter
 *
 * @author Adam Schubert
 */
class CountryPresenter extends SecuredPresenter
{
    /** @var CountryRepository @inject */
    public $userCountryRepository;

    /** @var CountryGridFactory @inject */
    public $userCountryGridFactory;

    /** @var CountryFormFactory @inject */
    public $userCountryFormFactory;

    /** @var Country */
    private $userCountryFormEntity;


    /**
     * @isAllowed(user,countryEdit)
     */
    public function actionDefault()
    {
        $this->template->h1 = 'Přehled Států';
    }

    /**
     * @param integer $id
     * @isAllowed(user,countryEdit)
     */
    public function actionEdit($id)
    {
        if ($id) {
            $country = $this->userCountryRepository->getOneById($id);
            if (!$country) {
                $this->error();
            }
            $this->template->h1 = sprintf('Stát „%s“', $country->getName());
            $this->userCountryFormEntity = $country;
        } else {
            $this->template->h1 = 'Založení nového Státu';
        }
    }

    /**
     * @return \AdminModule\Components\User\CountryGrid
     */
    public function createComponentGridCountry()
    {
        $control = $this->userCountryGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Country has been successfully deleted', 'alert-success');
            $this->redirect('Country:');
        };
        return $control;
    }

    /**
     * @return \AdminModule\Components\User\CountryForm
     */
    public function createComponentFormCountry()
    {
        $control = $this->userCountryFormFactory->create($this->userCountryFormEntity);
        $control->onSuccess[] = function(){
            $this->flashMessage('Stát bylo úspěšně uložen', 'alert-success');
            $this->redirect('Country:');
        };
        return $control;
    }

}
