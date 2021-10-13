<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;

use Dravencms\Model\Location\Entities\Country;
use Dravencms\Database\EntityManager;


class CountryRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|Country|string */
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
     * @param string $name
     * @param Country|null $ignoreCountry
     * @return bool
     */
    public function isCountryNameFree(string $name, Country $ignoreCountry = null): bool
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
     * @param string $code
     * @param Country|null $ignoreCountry
     * @return bool
     */
    public function isCountryCodeFree(string $code, Country $ignoreCountry = null): bool
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
     * @return mixed
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
    public function getById(int $id): array
    {
        return $this->countryRepository->findBy(['id' => $id]);
    }

    /**
     * @param $name
     * @return null|Country
     */
    public function getOneByName(string $name): ?Country
    {
        return $this->countryRepository->findOneBy(['name' => $name]);
    }

    /**
     * @param $id
     * @return null|Country
     */
    public function getOneById(int $id): ?Country
    {
        return $this->countryRepository->find($id);
    }

    /**
     * @return array
     */
    public function getPairs(): array
    {
        return $this->countryRepository->findPairs('name');
    }
}