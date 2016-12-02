<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;

use Dravencms\Model\Location\Entities\Country;
use Kdyby\Doctrine\EntityManager;
use Nette;

class CountryRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $countryRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * CountryRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->countryRepository = $entityManager->getRepository(Country::class);
    }

    /**
     * @param $name
     * @param Country|null $ignoreCountry
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isCountryNameFree($name, Country $ignoreCountry = null)
    {
        $qb = $this->countryRepository->createQueryBuilder('c')
            ->select('c')
            ->where('c.name = :name')
            ->setParameters([
                'name' => $name
            ]);

        if ($ignoreCountry)
        {
            $qb->andWhere('c != :ignoreCountry')
                ->setParameter('ignoreCountry', $ignoreCountry);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @param $code
     * @param Country|null $ignoreCountry
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isCountryCodeFree($code, Country $ignoreCountry = null)
    {
        $qb = $this->countryRepository->createQueryBuilder('c')
            ->select('c')
            ->where('c.code = :code')
            ->setParameters([
                'code' => $code
            ]);

        if ($ignoreCountry)
        {
            $qb->andWhere('c != :ignoreCountry')
                ->setParameter('ignoreCountry', $ignoreCountry);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getCountryQueryBuilder()
    {
        $qb = $this->countryRepository->createQueryBuilder('c')
            ->select('c');
        return $qb;
    }

    /**
     * @param $id
     * @return Country[]
     */
    public function getById($id)
    {
        return $this->countryRepository->findBy(['id' => $id]);
    }

    /**
     * @param $name
     * @return null|Country
     */
    public function getOneByName($name)
    {
        return $this->countryRepository->findOneBy(['name' => $name]);
    }

    /**
     * @param $id
     * @return null|Country
     */
    public function getOneById($id)
    {
        return $this->countryRepository->find($id);
    }

    /**
     * @return array
     */
    public function getPairs()
    {
        return $this->countryRepository->findPairs('name');
    }
}