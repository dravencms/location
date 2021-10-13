<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\Components\Location\StreetForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseForm;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Location\Entities\Street;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\StreetRepository;
use Dravencms\Model\Location\Repository\ZipCodeRepository;
use Dravencms\Database\EntityManager;
use Nette\Application\UI\Form;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class StreetForm extends BaseControl
{
    /** @var Street|null */
    private $street = null;

    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var StreetRepository */
    private $streetRepository;

    /** @var CityRepository */
    private $cityRepository;

    /** @var ZipCodeRepository */
    private $zipCodeRepository;

    /** @var EntityManager */
    private $entityManager;

    public $onSuccess = [];

    /**
     * StreetForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param StreetRepository $streetRepository
     * @param CityRepository $cityRepository
     * @param ZipCodeRepository $zipCodeRepository
     * @param EntityManager $entityManager
     * @param Street|null $street
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        StreetRepository $streetRepository,
        CityRepository $cityRepository,
        ZipCodeRepository $zipCodeRepository,
        EntityManager $entityManager,
        Street $street = null
    ) {
        $this->street = $street;
        $this->baseFormFactory = $baseFormFactory;
        $this->streetRepository = $streetRepository;
        $this->cityRepository = $cityRepository;
        $this->zipCodeRepository = $zipCodeRepository;
        $this->entityManager = $entityManager;

        if ($this->street)
        {
            $this['form']->setDefaults([
                'name' => $this->street->getName(),
                'zipCode' => $this->street->getZipCode()->getId()
            ]);
        }
    }

    /**
     * @return BaseForm
     */
    public function createComponentForm(): BaseForm
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Prosím zadejte jméno.');

        $zipCities = [];
        foreach ($this->cityRepository->getAll() AS $city) {
            $zips = [];
            foreach ($city->getZipCodes() AS $zip) {
                $zips[$zip->getId()] = $zip->getName();
            }
            $zipCities[$city->getName()] = $zips;
        }

        $form->addSelect('zipCode', null, $zipCities);
        $form->addSubmit('send');

        $form->onValidate[] = [$this, 'onValidateForm'];
        $form->onSuccess[] = [$this, 'onSuccessForm'];
        return $form;
    }

    /**
     * @param Form $form
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onValidateForm(Form $form): void
    {
        $values = $form->getValues();

        $zipCode = $this->zipCodeRepository->getOneById($values->zipCode);

        if (!$this->streetRepository->isStreetNameFree($values->name, $zipCode, $this->street))
        {
            $form->addError('Tato ulice již existuje.');
        }

        //Kontrola opraveni
        if (!$this->presenter->isAllowed('location', 'streetEdit')) {
            $form->addError('Nemáte oprávění editovat ulici.');
        }
    }

    /**
     * @param Form $form
     */
    public function onSuccessForm(Form $form): void
    {
        $values = $form->getValues();

        $zipCode = $this->zipCodeRepository->getOneById($values->zipCode);
        
        if ($this->street)
        {
            $street = $this->street;
            $street->setName($values->name);
            $street->setZipCode($zipCode);
        }
        else
        {
            $street = new Street($zipCode, $values->name);
        }

        $this->entityManager->persist($street);
        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->panelHeading = ($this->street ? 'Editation of '.$this->street->getName().' Street' : 'New street');
        $template->setFile(__DIR__ . '/StreetForm.latte');
        $template->render();
    }
}