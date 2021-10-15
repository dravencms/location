<?php declare(strict_types = 1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\LocationModule;


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
     * @isAllowed(location,streetEdit)
     */
    public function actionDefault(): void
    {
        $this->template->h1 = 'Přehled ulic';
    }

    /**
     * @param integer|null $id
     * @isAllowed(location,streetEdit)
     * @throws \Exception
     */
    public function actionEdit(int $id = null): void
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
     * @return \Dravencms\AdminModule\Components\Location\StreetGrid\StreetGrid
     */
    public function createComponentGridStreet(): StreetGrid
    {
        $control = $this->userStreetGridFactory->create();
        $control->onDelete[] = function(){
            $this->flashMessage('Street has been successfully deleted', Flash::SUCCESS);
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
            $this->flashMessage('Ulice byla úspěšně uložena', Flash::SUCCESS);
            $this->redirect('Street:');
        };
        return $component;
    }
}
