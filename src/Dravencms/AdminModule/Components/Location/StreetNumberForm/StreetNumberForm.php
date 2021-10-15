<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\Components\Location\StreetNumberForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Location\Entities\StreetNumber;
use Dravencms\Model\Location\Repository\StreetNumberRepository;
use Dravencms\Model\Location\Repository\StreetRepository;
use Dravencms\Database\EntityManager;
use Dravencms\Components\BaseForm\Form;
use Nette\Security\User;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class StreetNumberForm extends BaseControl
{
    /** @var StreetNumber|null */
    private $streetNumber = null;

    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var StreetNumberRepository */
    private $streetNumberRepository;

    /** @var StreetRepository */
    private $streetRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var User */
    private $user;

    public $onSuccess = [];

    /**
     * StreetNumberForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param StreetNumberRepository $streetNumberRepository
     * @param StreetRepository $streetRepository
     * @param EntityManager $entityManager
     * @param User $user
     * @param StreetNumber|null $streetNumber
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        StreetNumberRepository $streetNumberRepository,
        StreetRepository $streetRepository,
        EntityManager $entityManager,
        User $user,
        StreetNumber $streetNumber = null
    ) {
        $this->streetNumber = $streetNumber;
        $this->baseFormFactory = $baseFormFactory;
        $this->streetNumberRepository = $streetNumberRepository;
        $this->streetRepository = $streetRepository;
        $this->entityManager = $entityManager;
        $this->user = $user;

        if ($this->streetNumber)
        {
            $this['form']->setDefaults([
                'name' => $this->streetNumber->getName(),
                'street' => $this->streetNumber->getStreet()->getId()
            ]);
        }
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Prosím zadejte jméno.');

        $zipCities = [];
        foreach ($this->streetRepository->getAll() AS $street) {
            $zipCities[$street->getId()] = $street->getName();
        }

        $form->addSelect('street', null, $zipCities);
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

        $street = $this->streetRepository->getOneById($values->street);

        if (!$this->streetNumberRepository->isStreetNumberNameFree($values->name, $street, $this->streetNumber))
        {
            $form->addError('Toto číslo ulice již existuje.');
        }

        //Kontrola opraveni
        if (!$this->user->isAllowed('location', 'streetEdit')) {
            $form->addError('Nemáte oprávění editovat číslo ulice.');
        }
    }

    /**
     * @param Form $form
     */
    public function onSuccessForm(Form $form): void
    {
        $values = $form->getValues();

        $street = $this->streetRepository->getOneById($values->street);
        
        if ($this->streetNumber)
        {
            $streetNumber = $this->streetNumber;
            $streetNumber->setName($values->name);
            $streetNumber->setStreet($street);
        }
        else
        {
            $streetNumber = new StreetNumber($street, $values->name);
        }

        $this->entityManager->persist($streetNumber);
        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->panelHeading = ($this->streetNumber ? 'Editation of '.$this->streetNumber->getName().' Street Number' : 'New street number');
        $template->setFile(__DIR__ . '/StreetNumberForm.latte');
        $template->render();
    }
}