<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;

use Dravencms\Model\Location\Entities\City;
use Dravencms\Model\Location\Entities\Country;
use Dravencms\Model\Location\Entities\ZipCode;
use Kdyby\Doctrine\EntityManager;
use Nette;

/**
 * Class ZipCodeRepository
 * @package Dravencms\Model\User\Repository
 */
class ZipCodeRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
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
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getZipCodeQueryBuilder()
    {
        $qb = $this->zipCodeRepository->createQueryBuilder('z')
            ->select('z');
        return $qb;
    }

    /**
     * @param $id
     * @return null|ZipCode
     */
    public function getOneById($id)
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
     * @param $name
     * @param Country $country
     * @param ZipCode|null $ignoreZipCode
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isZipCodeFree($name, Country $country, ZipCode $ignoreZipCode = null)
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
     * @param $name
     * @param City|null $city
     * @return ZipCode[]
     */
    public function findByName($name, City $city = null)
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
    public function getOneByName($name, City $city = null)
    {
        $criteria = ['name' => $name];
        if (!is_null($city))
        {
            $criteria['city'] = $city;
        }
        return $this->zipCodeRepository->findOneBy($criteria);
    }
}