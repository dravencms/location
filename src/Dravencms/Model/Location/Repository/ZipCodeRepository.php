<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;

use Dravencms\Model\Location\Entities\City;
use Dravencms\Model\Location\Entities\Country;
use Dravencms\Model\Location\Entities\ZipCode;
use Dravencms\Database\EntityManager;


/**
 * Class ZipCodeRepository
 * @package Dravencms\Model\User\Repository
 */
class ZipCodeRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|ZipCode|string */
    private $zipCodeRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * MenuRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->zipCodeRepository = $entityManager->getRepository(ZipCode::class);
    }

    /**
     * @return mixed
     */
    public function getZipCodeQueryBuilder()
    {
        $qb = $this->zipCodeRepository->createQueryBuilder('z')
            ->select('z');
        return $qb;
    }

    /**
     * @param int $id
     * @return null|ZipCode
     */
    public function getOneById(int $id): ?ZipCode
    {
        return $this->zipCodeRepository->find($id);
    }

    /**
     * @param integer|array $id
     * @return ZipCode[]
     */
    public function getById($id)
    {
        return $this->zipCodeRepository->findBy(['id' => $id]);
    }

    /**
     * @param string $name
     * @param Country $country
     * @param ZipCode|null $ignoreZipCode
     * @return bool
     */
    public function isZipCodeFree(string $name, Country $country, ZipCode $ignoreZipCode = null): bool
    {
        $qb = $this->zipCodeRepository->createQueryBuilder('z')
            ->select('z')
            ->join('z.city', 'c')
            ->join('c.country', 'co')
            ->where('z.name = :name')
            ->andWhere('co = :country')
            ->setParameters([
                'name' => $name,
                'country' => $country
            ]);

        if ($ignoreZipCode)
        {
            $qb->andWhere('z != :ignoreZipCode')
                ->setParameter('ignoreZipCode', $ignoreZipCode);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @param string $name
     * @param City|null $city
     * @return ZipCode[]
     */
    public function findByName(string $name, City $city = null)
    {
        $qb = $this->zipCodeRepository->createQueryBuilder('z')
            ->select('z')
            ->where('z.name LIKE :name')
            ->setParameter('name', '%'.$name.'%');

        if ($city)
        {
            $qb->andWhere('z.city = :city')
                ->setParameter('city', $city);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param $name
     * @param City|null $city
     * @return null|ZipCode
     */
    public function getOneByName(string $name, City $city = null): ?ZipCode
    {
        $criteria = ['name' => $name];
        if (!is_null($city))
        {
            $criteria['city'] = $city;
        }
        return $this->zipCodeRepository->findOneBy($criteria);
    }
}