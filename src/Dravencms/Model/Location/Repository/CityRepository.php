<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;


use Dravencms\Model\Location\Entities\City;
use Dravencms\Model\Location\Entities\Country;
use Kdyby\Doctrine\EntityManager;
use Nette;

class CityRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
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
     * @return array
     */
    public function getPairs()
    {
        return $this->cityRepository->findPairs('name');
    }

    /**
     * @param $id
     * @return null|City
     */
    public function getOneById($id)
    {
        return $this->cityRepository->find($id);
    }

    /**
     * @param $id
     * @return City[]
     */
    public function getById($id)
    {
        return $this->cityRepository->findBy(['id' => $id]);
    }

    /**
     * @return City[]
     */
    public function getAll()
    {
        return $this->cityRepository->findAll();
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getCityQueryBuilder()
    {
        $qb = $this->cityRepository->createQueryBuilder('c')
            ->select('c');
        return $qb;
    }

    /**
     * @param $name
     * @param Country $country
     * @param City|null $ignoreCity
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isCityNameFree($name, Country $country, City $ignoreCity = null)
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
     * @param $name
     * @param Country|null $country
     * @return City[]
     */
    public function findByName($name, Country $country = null)
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
     * @param $name
     * @param Country|null $country
     * @return null|City
     */
    public function getOneByName($name, Country $country = null)
    {
        $criteria = ['name' => $name];
        if (!is_null($country))
        {
            $criteria['country'] = $country;
        }
        return $this->cityRepository->findOneBy($criteria);
    }
}