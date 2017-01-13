<?php
/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Dravencms\AdminModule\Components\Location\RegionForm;


use Doctrine\Common\Collections\ArrayCollection;
use Dravencms\Model\Location\Entities\Region;
use Dravencms\Model\Location\Repository\RegionRepository;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Location\Repository\CityRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * Description of CompanyForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class RegionForm extends Control
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var RegionRepository */
    private $regionRepository;

    /** @var CityRepository */
    private $cityRepository;

    /** @var Region|null */
    private $region;

    /** @var null|Region */
    private $parentRegion = null;

    /** @var array */
    public $onSuccess = [];


    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        RegionRepository $regionRepository,
        CityRepository $cityRepository,
        Region $region = null,
        Region $parentRegion = null
    ) {
        parent::__construct();
        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;

        $this->regionRepository = $regionRepository;
        $this->cityRepository = $cityRepository;
        $this->region = $region;
        $this->parentRegion = $parentRegion;

        if ($this->region) {

            $cities = [];
            
            foreach ($this->region->getCities() AS $city)
            {
                $cities[] = $city->getId();
            }

            $default = [
                'name' => $this->region->getName(),
                'isActive' => $this->region->isActive(),
                'cities' => $cities
            ];

        } else {
            $default = [
                'isActive' => true
            ];
        }

        $this['form']->setDefaults($default);
    }

    protected function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        $form->addText('name')
            ->setRequired('Prosím zadejte název firmy.');

        $cities = [];

        foreach($this->cityRepository->getAll() AS $city)
        {
            $cities[$city->getid()] = $city->getName();
        }

        $form->addMultiSelect('cities', null, $cities);

        $form->addCheckbox('isActive');

        $form->addSubmit('send');

        $form->onValidate[] = [$this, 'editFormValidate'];
        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function editFormValidate(Form $form)
    {
        $values = $form->getValues();
        if (!$this->regionRepository->isNameFree($values->name, $this->parentRegion, $this->region))
        {
            $form->addError('Region item with this name already exists!');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form)
    {
        $values = $form->getValues();


        $cities = new ArrayCollection($this->cityRepository->getById($values->cities));

        if ($this->region) {
            $region = $this->region;
            $region->setName($values->name);
            $region->setIsActive($values->isActive);
            $region->setCities($cities);
            $this->entityManager->persist($region);
        } else {
            $region = new Region(
                $values->name
            );
            $region->setCities($cities);
            if ($this->parentRegion)
            {
                $this->regionRepository->persistAsLastChildOf($region, $this->parentRegion);
            }
            else
            {
                $this->entityManager->persist($region);
            }
        }

        $this->entityManager->flush();

        $this->onSuccess($region);
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/RegionForm.latte');
        $template->render();
    }
}