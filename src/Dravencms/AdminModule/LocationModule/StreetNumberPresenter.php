<?php declare(strict_types = 1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Dravencms\AdminModule\LocationModule;


use Dravencms\AdminModule\Components\Location\StreetNumberForm\StreetNumberForm;
use Dravencms\AdminModule\Components\Location\StreetNumberForm\StreetNumberFormFactory;
use Dravencms\AdminModule\Components\Location\StreetNumberGrid\StreetNumberGrid;
use Dravencms\AdminModule\Components\Location\StreetNumberGrid\StreetNumberGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Model\Location\Entities\StreetNumber;
use Dravencms\Model\Location\Repository\StreetNumberRepository;

/**
 * Description of StreetPresenter
 *
 * @author Adam Schubert
 */
class StreetNumberPresenter extends SecuredPresenter
{
    /** @var StreetNumberRepository @inject */
    public $userStreetNumberRepository;

    /** @var StreetNumberGridFactory @inject */
    public $userStreetNumberGridFactory;

    /** @var StreetNumberFormFactory @inject */
    public $userStreetNumberFormFactory;
    
    /** @var StreetNumber|null */
    private $streetNumber = null;

    /**
     * @isAllowed(location,streetEdit)
     */
    public function actionDefault(): void
    {
        $this->template->h1 = 'Přehled čísel ulic';
    }

    /**
     * @param integer $id
     * @isAllowed(location,streetEdit)
     * @throws \Exception
     */
    public function actionEdit(int $id): void
    {
        if ($id) {
            $streetNumber = $this->userStreetNumberRepository->getOneById($id);
            if (!$streetNumber) {
                $this->error();
            }
            $this->streetNumber = $streetNumber;
            $this->template->h1 = sprintf('Číslo ulice „%s“', $streetNumber->getName());
        } else {
            $this->template->h1 = 'Vytvoření nového čísla ulice';
        }
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\StreetNumberGrid\StreetNumberGrid
     */
    public function createComponentGridStreetNumber(): StreetNumberGrid
    {
        $control = $this->userStreetNumberGridFactory->create();
        $control->onDelete[] = function(){
            $this->flashMessage('Street number has been successfully deleted', 'alert-success');
            $this->redirect('StreetNumber:');
        };
        return $control;
    }

    /**
     * @return \Dravencms\AdminModule\Components\Location\StreetNumberForm\StreetNumberForm
     */
    public function createComponentFormStreetNumber(): StreetNumberForm
    {
        $component = $this->userStreetNumberFormFactory->create($this->streetNumber);
        $component->onSuccess[] = function()
        {
            $this->flashMessage('Street number byla úspěšně uložena', 'alert-success');
            $this->redirect('StreetNumber:');
        };
        return $component;
    }
}
