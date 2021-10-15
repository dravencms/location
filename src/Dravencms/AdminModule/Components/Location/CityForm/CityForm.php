<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\Components\Location\CityForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseForm;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Location\Entities\City;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\CountryRepository;
use Dravencms\Database\EntityManager;
use Dravencms\Components\BaseForm\Form;
use Nette\Security\User;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class CityForm extends BaseControl
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

    /** @var User */
    private $user;

    public $onSuccess = [];

    public function __construct(
        BaseFormFactory $baseFormFactory,
        CityRepository $cityRepository,
        CountryRepository $countryRepository,
        EntityManager $entityManager,
        User $user,
        City $city = null
    ) {
        $this->city = $city;
        $this->baseFormFactory = $baseFormFactory;
        $this->cityRepository = $cityRepository;
        $this->countryRepository = $countryRepository;
        $this->entityManager = $entityManager;
        $this->user = $user;

        if ($this->city)
        {
            $this['form']->setDefaults([
                'name' => $this->city->getName(),
                'country' => $this->city->getCountry()->getId()
            ]);
        }
    }

    public function createComponentForm(): Form
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
    public function onValidateForm(Form $form): void
    {
        $values = $form->getValues();

        $country = $this->countryRepository->getOneById($values->country);

        if (!$this->cityRepository->isCityNameFree($values->name, $country, $this->city))
        {
            $form->addError('Toto mesto již existuje.');
        }
        
        //Kontrola opraveni
        if (!$this->user->isAllowed('location', 'cityEdit')) {
            $form->addError('Nemáte oprávění editovat mesta.');
        }
    }

    /**
     * @param Form $form
     */
    public function onSuccessForm(Form $form): void
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

    public function render(): void
    {
        $template = $this->template;
        $template->panelHeading = ($this->city ? 'Editation of '.$this->city->getName().' city' : 'New City');
        $template->setFile(__DIR__ . '/CityForm.latte');
        $template->render();
    }
}