<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;


use Dravencms\Model\Location\Entities\Street;
use Dravencms\Model\Location\Entities\StreetNumber;
use Kdyby\Doctrine\EntityManager;
use Nette;

class StreetNumberRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
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
    public function getOneById($id)
    {
        return $this->streetNumberRepository->find($id);
    }

    /**
     * @param $id
     * @return StreetNumber[]
     */
    public function getById($id)
    {
        return $this->streetNumberRepository->findBy(['id' => $id]);
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getStreetNumberQueryBuilder()
    {
        $qb = $this->streetNumberRepository->createQueryBuilder('sn')
            ->select('sn');
        return $qb;
    }

    /**
     * @param $name
     * @param Street $street
     * @param StreetNumber|null $ignoreStreetNumber
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isStreetNumberNameFree($name, Street $street, StreetNumber $ignoreStreetNumber = null)
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
}