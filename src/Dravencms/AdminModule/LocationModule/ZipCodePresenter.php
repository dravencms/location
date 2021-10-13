<?php declare(strict_types = 1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\LocationModule;


use Dravencms\AdminModule\Components\Location\ZipCodeForm\ZipCodeForm;
use Dravencms\AdminModule\Components\Location\ZipCodeForm\ZipCodeFormFactory;
use Dravencms\AdminModule\Components\Location\ZipCodeGrid\ZipCodeGrid;
use Dravencms\AdminModule\Components\Location\ZipCodeGrid\ZipCodeGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Location\Entities\ZipCode;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\ZipCodeRepository;

/**
 * Description of ZipCodePresenter
 *
 * @author Adam Schubert
 */
class ZipCodePresenter extends SecuredPresenter
{
    /** @var ZipCodeRepository @inject */
    public $userCountryRepository;

    /** @var CityRepository @inject */
    public $userCityRepository;

    /** @var ZipCodeGridFactory @inject */
    public $userZipCodeGridFactory;

    /** @var ZipCodeFormFactory @inject */
    public $userZipCodeFormFactory;

    /** @var ZipCode */
    private $userZipCodeFormEntity;


    /**
     * @isAllowed(location,zipCodeEdit)
     */
    public function actionDefault(): void
    {
        $this->template->h1 = 'Přehled PSČ';
    }

    /**
     * @param integer $id
     * @isAllowed(location,zipCodeEdit)
     */
    public function actionEdit(int $id): void
    {
        if ($id) {
            $zipCode = $this->userCountryRepository->getOneById($id);
            if (!$zipCode) {
                $this->error();
            }
            $this->template->h1 = sprintf('PSČ „%s“', $zipCode->getName());
            $this->userZipCodeFormEntity = $zipCode;
        } else {
            $this->template->h1 = 'Založení nového PSČ';
        }
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\ZipCodeGrid\ZipCodeGrid
     */
    public function createComponentGridZipCode(): ZipCodeGrid
    {
        $control = $this->userZipCodeGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Zip Code has been successfully deleted', 'alert-success');
            $this->redirect('ZipCode:');
        };
        return $control;
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\ZipCodeForm\ZipCodeForm
     */
    public function createComponentFormZipCode(): ZipCodeForm
    {
        $control = $this->userZipCodeFormFactory->create($this->userZipCodeFormEntity);
        $control->onSuccess[] = function(){
            $this->flashMessage('PSČ bylo úspěšně uloženo', 'alert-success');
            $this->redirect('ZipCode:');
        };
        return $control;
    }

}
