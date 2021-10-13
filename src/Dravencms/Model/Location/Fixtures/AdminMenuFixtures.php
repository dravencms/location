<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Dravencms\Model\Admin\Entities\Menu;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

class AdminMenuFixtures extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        if (!class_exists(Menu::class)) {
            trigger_error('dravencms/admin module not found, dravencms/location module wont install Admin menu entries', E_USER_NOTICE);
            return;
        }

        $menu = $manager->getRepository(Menu::class);
        // Location
        $root = new Menu('Locations', null, 'fa-globe', $this->getReference('user-acl-operation-location-edit'), null);
        $manager->persist($root);

        $child = new Menu('Streets', ':Admin:Location:Street', 'fa-language', $this->getReference('user-acl-operation-location-streetEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Street Numbers', ':Admin:Location:StreetNumber', 'fa-subscript', $this->getReference('user-acl-operation-location-streetEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Cities', ':Admin:Location:City', 'fa-map-marker', $this->getReference('user-acl-operation-location-cityEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Zip codes', ':Admin:Location:ZipCode', 'fa-map-signs', $this->getReference('user-acl-operation-location-zipCodeEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Countries', ':Admin:Location:Country', 'fa-globe', $this->getReference('user-acl-operation-location-countryEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Regions', ':Admin:Location:Region', 'fa-compass', $this->getReference('user-acl-operation-location-regionEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $manager->flush();
    }
    /**
     * Get the order of this fixture
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return ['Dravencms\Model\Location\Fixtures\AclOperationFixtures'];
    }
}