<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;


use Dravencms\Model\Location\Entities\Street;
use Dravencms\Model\Location\Entities\StreetNumber;
use Dravencms\Database\EntityManager;


class StreetNumberRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|StreetNumber|string */
    private $streetNumberRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * StreetNumberRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->streetNumberRepository = $entityManager->getRepository(StreetNumber::class);
    }

    /**
     * @param $id
     * @return null|StreetNumber
     */
    public function getOneById(int $id): ?StreetNumber
    {
        return $this->streetNumberRepository->find($id);
    }

    /**
     * @param array|int $id
     * @return StreetNumber[]
     */
    public function getById($id)
    {
        return $this->streetNumberRepository->findBy(['id' => $id]);
    }

    /**
     * @return mixed
     */
    public function getStreetNumberQueryBuilder()
    {
        $qb = $this->streetNumberRepository->createQueryBuilder('sn')
            ->select('sn');
        return $qb;
    }

    /**
     * @param string $name
     * @param Street $street
     * @param StreetNumber|null $ignoreStreetNumber
     * @return bool
     */
    public function isStreetNumberNameFree(string $name, Street $street, StreetNumber $ignoreStreetNumber = null): bool
    {
        $qb = $this->streetNumberRepository->createQueryBuilder('sn')
            ->select('sn')
            ->where('sn.name = :name')
            ->andWhere('sn.street = :street')
            ->setParameters([
                'name' => $name,
                'street' => $street
            ]);

        if ($ignoreStreetNumber)
        {
            $qb->andWhere('s != :ignoreStreetNumber')
                ->setParameter('ignoreStreetNumber', $ignoreStreetNumber);
        }

        return (is_null($qb->getQuery()->getOneOrNullResult()));
    }

    /**
     * @param string $name
     * @param Street|null $street
     * @return StreetNumber[]
     */
    public function findByName(string $name, Street $street = null)
    {
        $qb = $this->streetNumberRepository->createQueryBuilder('s')
            ->select('s')
            ->where('s.name LIKE :name')
            ->setParameter('name', '%'.$name.'%');

        if ($street)
        {
            $qb->andWhere('s.street = :street')
                ->setParameter('street', $street);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param $name
     * @param Street|null $street
     * @return null|StreetNumber
     */
    public function getOneByName(string $name, Street $street = null): ?StreetNumber
    {
        $criteria = ['name' => $name];
        if (!is_null($street))
        {
            $criteria['street'] = $street;
        }

        return $this->streetNumberRepository->findOneBy($criteria);
    }
}