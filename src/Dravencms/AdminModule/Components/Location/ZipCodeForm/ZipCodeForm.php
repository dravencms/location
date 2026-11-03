<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\Components\Location\ZipCodeForm;

use Contributte\Translation\Wrappers\Message;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Location\Entities\ZipCode;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\ZipCodeRepository;
use Dravencms\Database\EntityManager;
use Dravencms\Components\BaseForm\Form;
use Nette\Security\User;

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

    /** @var User */
    private $user;

    public $onSuccess = [];

    /**
     * ZipCodeForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param ZipCodeRepository $streetRepository
     * @param CityRepository $zipCodeRepository
     * @param EntityManager $entityManager
     * @param User $user
     * @param ZipCode|null $zipCode
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        ZipCodeRepository $streetRepository,
        CityRepository $zipCodeRepository,
        EntityManager $entityManager,
        User $user,
        ?ZipCode $zipCode = null
    ) {
        $this->zipCode = $zipCode;
        $this->baseFormFactory = $baseFormFactory;
        $this->zipCodeRepository = $streetRepository;
        $this->cityRepository = $zipCodeRepository;
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('location.admin.validation.zipCodeRequired');

        $form->addSelect('city', null, $this->cityRepository->getPairs());

        $form->addSubmit('send', 'location.admin.action.save');

        $form->onValidate[] = [$this, 'onValidateForm'];
        $form->onSuccess[] = [$this, 'onSuccessForm'];
        if ($this->zipCode) {
            $form->setDefaults([
                'name' => $this->zipCode->getName(),
                'city' => $this->zipCode->getCity()->getId(),
            ]);
        }

        return $form;
    }

    /**
     * @param Form $form
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function onValidateForm(Form $form)
    {
        $values = $form->getValues();

        $city = $this->cityRepository->getOneById($values->city);

        if (!$this->zipCodeRepository->isZipCodeFree($values->name, $city->getCountry(), $this->zipCode))
        {
            $form->addError('location.admin.validation.zipCodeTaken');
        }

        //Kontrola opraveni
        if (!$this->user->isAllowed('location', 'zipCodeEdit')) {
            $form->addError('location.admin.validation.zipCodeForbidden');
        }
    }

    /**
     * @param Form $form
     */
    public function onSuccessForm(Form $form): void
    {
        $this->requirePermission('location', 'zipCodeEdit');
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

    public function render(): void
    {
        $template = $this->template;
        $template->panelHeading = ($this->zipCode ? new Message('location.admin.title.editZipCode', ['name' => $this->zipCode->getName()]) : 'location.admin.title.newZipCode');
        $template->setFile(__DIR__ . '/ZipCodeForm.latte');
        $template->render();
    }
}