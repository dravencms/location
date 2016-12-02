<?php

namespace Dravencms\AdminModule\Components\Location\CountryForm;

use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Location\Entities\Country;
use Dravencms\Model\Location\Repository\CountryRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class CountryForm extends Control
{
    /** @var Country|null */
    private $country = null;

    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var CountryRepository */
    private $countryRepository;

    /** @var EntityManager */
    private $entityManager;

    public $onSuccess = [];

    public function __construct(
        BaseFormFactory $baseFormFactory,
        CountryRepository $streetRepository,
        EntityManager $entityManager,
        Country $country = null
    ) {
        parent::__construct();
        $this->country = $country;
        $this->baseFormFactory = $baseFormFactory;
        $this->countryRepository = $streetRepository;
        $this->entityManager = $entityManager;

        if ($this->country)
        {
            $this['form']->setDefaults([
                'name' => $this->country->getName(),
                'code' => $this->country->getCode()
            ]);
        }
    }

    public function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Prosím zadejte Stat.');

        $form->addText('code')
            ->setRequired('Prosím zadejte kod statu.');

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

        if (!$this->countryRepository->isCountryNameFree($values->name, $this->country))
        {
            $form->addError('Tato zeme již existuje.');
        }

        if (!$this->countryRepository->isCountryCodeFree($values->code, $this->country))
        {
            $form->addError('Tento kod zeme již existuje.');
        }

        //Kontrola opraveni
        if (!$this->presenter->isAllowed('location', 'countryEdit')) {
            $form->addError('Nemáte oprávění editovat zeme.');
        }
    }

    /**
     * @param Form $form
     */
    public function onSuccessForm(Form $form)
    {
        $values = $form->getValues();

        if ($this->country)
        {
            $country = $this->country;
            $country->setName($values->name);
            $country->setCode($values->code);
        }
        else
        {
            $country = new Country($values->name, $values->code);
        }

        $this->entityManager->persist($country);
        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render()
    {
        $template = $this->template;
        $template->panelHeading = ($this->country ? 'Editation of '.$this->country->getName().' country' : 'New Country');
        $template->setFile(__DIR__ . '/CountryForm.latte');
        $template->render();
    }
}