<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;


use Dravencms\Model\Location\Entities\Street;
use Dravencms\Model\Location\Entities\ZipCode;
use Kdyby\Doctrine\EntityManager;
use Nette;

class StreetRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $streetRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * StreetRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->streetRepository = $entityManager->getRepository(Street::class);
    }

    /**
     * @param $id
     * @return null|Street
     */
    public function getOneById($id)
    {
        return $this->streetRepository->find($id);
    }

    /**
     * @param $id
     * @return Street[]
     */
    public function getById($id)
    {
        return $this->streetRepository->findBy(['id' => $id]);
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getStreetQueryBuilder()
    {
        $qb = $this->streetRepository->createQueryBuilder('s')
            ->select('s');
        return $qb;
    }

    /**
     * @param $name
     * @param ZipCode $zipCode
     * @param Street|null $ignoreStreet
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isStreetNameFree($name, ZipCode $zipCode, Street $ignoreStreet = null)
    {
        $qb = $this->streetRepository->createQueryBuilder('s')
            ->select('s')
            ->where('s.name = :name')
            ->andWhere('s.zipCode = :zipCode')
            ->setParameters([
                'name' => $name,
                'zipCode' => $zipCode
            ]);

        if ($ignoreStreet)
        {
            $qb->andWhere('s != :ignoreStreet')
                ->setParameter('ignoreStreet', $ignoreStreet);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @return Street[]
     */
    public function getAll()
    {
        return $this->streetRepository->findAll();
    }

    /**
     * @param $name
     * @param ZipCode|null $zipCode
     * @return Street[]
     */
    public function findByName($name, ZipCode $zipCode = null)
    {
        $qb = $this->streetRepository->createQueryBuilder('s')
            ->select('s')
            ->where('s.name LIKE :name')
            ->setParameter('name', '%'.$name.'%');

        if ($zipCode)
        {
            $qb->andWhere('s.zipCode = :zipCode')
                ->setParameter('zipCode', $zipCode);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param $name
     * @param ZipCode $zipCode
     * @return null|Street
     */
    public function getOneByName($name, ZipCode $zipCode = null)
    {
        $criteria = ['name' => $name];
        if (!is_null($zipCode))
        {
            $criteria['zipCode'] = $zipCode;
        }
        return $this->streetRepository->findOneBy($criteria);
    }
}