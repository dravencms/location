<?php declare(strict_types = 1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\LocationModule;


use Dravencms\AdminModule\Components\Location\CountryForm\CountryForm;
use Dravencms\AdminModule\Components\Location\CountryForm\CountryFormFactory;
use Dravencms\AdminModule\Components\Location\CountryGrid\CountryGrid;
use Dravencms\AdminModule\Components\Location\CountryGrid\CountryGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Flash;
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
     * @isAllowed(location,countryEdit)
     */
    public function actionDefault(): void
    {
        $this->template->h1 = 'Přehled Států';
    }

    /**
     * @param integer|null $id
     * @isAllowed(location,countryEdit)
     */
    public function actionEdit(int $id = null): void
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
     * @return \Dravencms\AdminModule\Components\Location\CountryGrid\CountryGrid
     */
    public function createComponentGridCountry(): CountryGrid
    {
        $control = $this->userCountryGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Country has been successfully deleted', Flash::SUCCESS);
            $this->redirect('Country:');
        };
        return $control;
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\CountryForm\CountryForm
     */
    public function createComponentFormCountry(): CountryForm
    {
        $control = $this->userCountryFormFactory->create($this->userCountryFormEntity);
        $control->onSuccess[] = function(){
            $this->flashMessage('Stát byl úspěšně uložen', Flash::SUCCESS);
            $this->redirect('Country:');
        };
        return $control;
    }

}
