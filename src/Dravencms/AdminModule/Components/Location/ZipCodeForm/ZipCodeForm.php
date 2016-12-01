<?php

namespace Dravencms\AdminModule\Components\Location\ZipCodeForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Location\Entities\ZipCode;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\ZipCodeRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class ZipCodeForm extends BaseControl
{
    /** @var ZipCode|null */
    private $zipCode = null;

    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var ZipCodeRepository */
    private $zipCodeRepository;

    /** @var CityRepository */
    private $cityRepository;

    /** @var EntityManager */
    private $entityManager;

    public $onSuccess = [];

    /**
     * ZipCodeForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param ZipCodeRepository $streetRepository
     * @param CityRepository $zipCodeRepository
     * @param EntityManager $entityManager
     * @param ZipCode|null $zipCode
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        ZipCodeRepository $streetRepository,
        CityRepository $zipCodeRepository,
        EntityManager $entityManager,
        ZipCode $zipCode = null
    ) {
        parent::__construct();
        $this->zipCode = $zipCode;
        $this->baseFormFactory = $baseFormFactory;
        $this->zipCodeRepository = $streetRepository;
        $this->cityRepository = $zipCodeRepository;
        $this->entityManager = $entityManager;

        if ($this->zipCode)
        {
            $this['form']->setDefaults([
                'name' => $this->zipCode->getName(),
                'city' => $this->zipCode->getCity()->getId()
            ]);
        }
    }

    public function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Prosím zadejte PSČ.');

        $form->addSelect('city', null, $this->cityRepository->getPairs());

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

        $city = $this->cityRepository->getOneById($values->city);

        if (!$this->zipCodeRepository->isZipCodeFree($values->name, $city->getCountry(), $this->zipCode))
        {
            $form->addError('Toto PSC již existuje.');
        }

        //Kontrola opraveni
        if (!$this->presenter->isAllowed('user', 'zipCodeEdit')) {
            $form->addError('Nemáte oprávění editovat zip code.');
        }
    }

    /**
     * @param Form $form
     */
    public function onSuccessForm(Form $form)
    {
        $values = $form->getValues();

        $city = $this->cityRepository->getOneById($values->city);
        
        if ($this->zipCode)
        {
            $zipCode = $this->zipCode;
            $zipCode->setName($values->name);
            $zipCode->setCity($city);
        }
        else
        {
            $zipCode = new ZipCode($city, $values->name);
        }

        $this->entityManager->persist($zipCode);
        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render()
    {
        $template = $this->template;
        $template->panelHeading = ($this->zipCode ? 'Editation of '.$this->zipCode->getName().' ZIP code' : 'New ZIP code');
        $template->setFile(__DIR__ . '/ZipCodeForm.latte');
        $template->render();
    }
}