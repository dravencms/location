<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Fixtures;

use Dravencms\Model\Location\Entities\Country;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;


class CountryFixtures extends AbstractFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $countries = require_once __DIR__."/../../../../../../../../vendor/umpirsky/country-list/data/en_US/country.php";
        foreach ($countries AS $code => $name)
        {
            $country = new Country($name, $code);
            $manager->persist($country);
            $this->addReference('location-country-'.$code, $country);
        }
        $manager->flush();
    }
}