<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;


use Dravencms\Model\Location\Entities\City;
use Dravencms\Model\Location\Entities\Country;
use Dravencms\Model\Location\Entities\Region;
use Dravencms\Database\EntityManager;

class CityRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|City|string */
    private $cityRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * CityRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->cityRepository = $entityManager->getRepository(City::class);
    }

    /**
     * @return mixed
     */
    public function getPairs()
    {
        return $this->cityRepository->findPairs('name');
    }

    /**
     * @param int $id
     * @return City|null
     */
    public function getOneById(int $id): ?City
    {
        return $this->cityRepository->find($id);
    }

    /**
     * @param int $id
     * @return City[]
     */
    public function getById(int $id): array
    {
        return $this->cityRepository->findBy(['id' => $id]);
    }

    /**
     * @return City[]
     */
    public function getAll(): array
    {
        return $this->cityRepository->findAll();
    }

    /**
     * @return mixed
     */
    public function getCityQueryBuilder()
    {
        $qb = $this->cityRepository->createQueryBuilder('c')
            ->select('c');
        return $qb;
    }

    /**
     * @param string $name
     * @param Country $country
     * @param City|null $ignoreCity
     * @return bool
     */
    public function isCityNameFree(string $name, Country $country, City $ignoreCity = null): bool
    {
        $qb = $this->cityRepository->createQueryBuilder('c')
            ->select('c')
            ->where('c.name = :name')
            ->andWhere('c.country = :country')
            ->setParameters([
                'name' => $name,
                'country' => $country
            ]);

        if ($ignoreCity)
        {
            $qb->andWhere('c != :ignoreCity')
                ->setParameter('ignoreCity', $ignoreCity);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @param string $name
     * @param Country|null $country
     * @return City[]
     */
    public function findByName(string $name, Country $country = null): array
    {
        $qb = $this->cityRepository->createQueryBuilder('c')
            ->select('c')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%'.$name.'%');

        if ($country)
        {
            $qb->andWhere('c.country = :country')
                ->setParameter('country', $country);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param string $name
     * @param Country|null $country
     * @return null|City
     */
    public function getOneByName(string $name, Country $country = null): ?City
    {
        $criteria = ['name' => $name];
        if (!is_null($country))
        {
            $criteria['country'] = $country;
        }
        return $this->cityRepository->findOneBy($criteria);
    }

    /**
     * @param string $slug
     * @param Region|null $region
     * @param Country|null $country
     * @return City|null
     */
    public function getOneBySlug(string $slug, Region $region = null, Country $country = null): ?City
    {
        $criteria = ['slug' => $slug];
        if (!is_null($region))
        {
            $criteria['region'] = $region;
        }
        if (!is_null($country))
        {
            $criteria['country'] = $country;
        }
        return $this->cityRepository->findOneBy($criteria);
    }
}