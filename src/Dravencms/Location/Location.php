<?php declare(strict_types = 1);

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
use Dravencms\Database\EntityManager;
use Nette\SmartObject;

/**
 * Class Location
 * @package Dravencms\Location
 */
class Location
{
    use SmartObject;

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
     * @param int $countryId
     * @param string $cityName
     * @param string $zipCodeName
     * @param string $streetName
     * @param string $streetNumberName
     * @return StreetNumber|null
     */
    public function saveStreetNumber(int $countryId, string $cityName, string $zipCodeName, string $streetName, string $streetNumberName): ?StreetNumber
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
