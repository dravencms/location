<?php

namespace Dravencms\Location;
use Dravencms\Model\Location\Entities\City;
use Dravencms\Model\Location\Entities\Street;
use Dravencms\Model\Location\Entities\StreetNumber;
use Dravencms\Model\Location\Entities\ZipCode;
use Dravencms\Model\Location\Repository\CityRepository;
use Dravencms\Model\Location\Repository\CountryRepository;
use Dravencms\Model\Location\Repository\StreetNumberRepository;
use Dravencms\Model\Location\Repository\StreetRepository;
use Dravencms\Model\Location\Repository\ZipCodeRepository;
use Kdyby\Doctrine\EntityManager;


/**
 * Class Location
 * @package Dravencms\Location
 */
class Location extends \Nette\Object
{
    /** @var CountryRepository */
    private $countryRepository;

    /** @var CityRepository */
    private $cityRepository;

    /** @var ZipCodeRepository */
    private $zipCodeRepository;

    /** @var StreetRepository */
    private $streetRepository;

    /** @var StreetNumberRepository */
    private $streetNumberRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * Location constructor.
     * @param CountryRepository $countryRepository
     * @param CityRepository $cityRepository
     * @param ZipCodeRepository $zipCodeRepository
     * @param StreetRepository $streetRepository
     * @param StreetNumberRepository $streetNumberRepository
     * @param EntityManager $entityManager
     */
    public function __construct(
        CountryRepository $countryRepository,
        CityRepository $cityRepository,
        ZipCodeRepository $zipCodeRepository,
        StreetRepository $streetRepository,
        StreetNumberRepository $streetNumberRepository,
        EntityManager $entityManager
    )
    {
        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
        $this->zipCodeRepository = $zipCodeRepository;
        $this->streetRepository = $streetRepository;
        $this->streetNumberRepository = $streetNumberRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $countryId
     * @param $cityName
     * @param $zipCodeName
     * @param $streetName
     * @param $streetNumberName
     * @return StreetNumber|null
     */
    private function saveStreetNumber($countryId, $cityName, $zipCodeName, $streetName, $streetNumberName)
    {
        $country = $this->countryRepository->getOneById($countryId);
        $streetNumber = null;
        if ($cityName)
        {
            if (!$city = $this->cityRepository->getOneByName($cityName, $country)) {
                $city = new City($country, $cityName);
                $this->entityManager->persist($city);
            }
            if ($zipCodeName)
            {
                if (!$zipCode = $this->zipCodeRepository->getOneByName($zipCodeName, $city)) {
                    $zipCode = new ZipCode($city, $zipCodeName);
                    $this->entityManager->persist($zipCode);
                }
                if ($streetName)
                {
                    if (!$street = $this->streetRepository->getOneByName($streetName, $zipCode)) {
                        $street = new Street($zipCode, $streetName);
                        $this->entityManager->persist($street);
                    }
                    if ($streetNumberName)
                    {
                        if (!$streetNumber = $this->streetNumberRepository->getOneByName($streetNumberName, $street)) {
                            $streetNumber = new StreetNumber($street, $streetNumberName);
                            $this->entityManager->persist($streetNumber);
                        }
                    }
                }
            }
        }

        return $streetNumber;
    }
}
