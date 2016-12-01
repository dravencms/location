<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Dravencms\Model\Admin\Entities\Menu;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class AdminMenuFixtures extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $menu = $manager->getRepository(Menu::class);
        // Location
        $root = new Menu('Locations', null, 'fa-globe', $this->getReference('user-acl-operation-location-edit'), null);
        $manager->persist($root);

        $child = new Menu('Streets', ':Admin:User:Street', 'fa-language', $this->getReference('user-acl-operation-location-streetEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Street Numbers', ':Admin:User:StreetNumber', 'fa-subscript', $this->getReference('user-acl-operation-location-streetEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Cities', ':Admin:User:City', 'fa-map-marker', $this->getReference('user-acl-operation-location-cityEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Zip codes', ':Admin:User:ZipCode', 'fa-map-signs', $this->getReference('user-acl-operation-location-zipCodeEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $child = new Menu('Countries', ':Admin:User:Country', 'fa-globe', $this->getReference('user-acl-operation-location-countryEdit'));
        $manager->persist($child);
        $menu->persistAsLastChildOf($child, $root);
        $manager->flush();
    }
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getDependencies()
    {
        return ['Dravencms\Model\Location\Fixtures\AclOperationFixtures'];
    }
}