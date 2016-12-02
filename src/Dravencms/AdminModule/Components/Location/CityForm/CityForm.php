<?php

namespace Dravencms\AdminModule\Components\Location\CityForm;

use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Location\Entities\City;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\CountryRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class CityForm extends Control
{
    /** @var City|null */
    private $city = null;

    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var CityRepository */
    private $cityRepository;
    
    /** @var CountryRepository */
    private $countryRepository;

    /** @var EntityManager */
    private $entityManager;

    public $onSuccess = [];

    public function __construct(
        BaseFormFactory $baseFormFactory,
        CityRepository $cityRepository,
        CountryRepository $countryRepository,
        EntityManager $entityManager,
        City $city = null
    ) {
        parent::__construct();
        $this->city = $city;
        $this->baseFormFactory = $baseFormFactory;
        $this->cityRepository = $cityRepository;
        $this->countryRepository = $countryRepository;
        $this->entityManager = $entityManager;

        if ($this->city)
        {
            $this['form']->setDefaults([
                'name' => $this->city->getName(),
                'country' => $this->city->getCountry()->getId()
            ]);
        }
    }

    public function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Prosím zadejte Stat.');

        $form->addSelect('country', null, $this->countryRepository->getPairs())
            ->setRequired('Prosím zadejte zemi.');

        $form->addSubmit('send');

        $form->onValidate[] = [$this, 'onValidateForm'];
        $form->onSuccess[] = [$this, 'onSuccessForm'];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function onValidateForm(Form $form)
    {
        $values = $form->getValues();

        $country = $this->countryRepository->getOneById($values->country);

        if (!$this->cityRepository->isCityNameFree($values->name, $country, $this->city))
        {
            $form->addError('Toto mesto již existuje.');
        }
        
        //Kontrola opraveni
        if (!$this->presenter->isAllowed('location', 'cityEdit')) {
            $form->addError('Nemáte oprávění editovat mesta.');
        }
    }

    /**
     * @param Form $form
     */
    public function onSuccessForm(Form $form)
    {
        $values = $form->getValues();

        $country = $this->countryRepository->getOneById($values->country);

        if ($this->city)
        {
            $city = $this->city;
            $city->setName($values->name);
            $city->setCountry($country);
        }
        else
        {
            $city = new City($country, $values->name);
        }

        $this->entityManager->persist($city);
        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render()
    {
        $template = $this->template;
        $template->panelHeading = ($this->city ? 'Editation of '.$this->city->getName().' city' : 'New City');
        $template->setFile(__DIR__ . '/CityForm.latte');
        $template->render();
    }
}