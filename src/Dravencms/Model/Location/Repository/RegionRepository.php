<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;

use Doctrine\ORM\Query;
use Dravencms\Model\Location\Entities\Region;
use Kdyby\Doctrine\EntityManager;
use Nette;

class RegionRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $regionRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * RegionRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->regionRepository = $entityManager->getRepository(Region::class);
    }

    /**
     * @param $id
     * @return Region[]
     */
    public function getById($id)
    {
        return $this->regionRepository->findBy(['id' => $id]);
    }

    /**
     * @param $id
     * @return null|Region
     */
    public function getOneById($id)
    {
        return $this->regionRepository->find($id);
    }

    /**
     * @param $slug
     * @return null|Region
     */
    public function getOneBySlug($slug)
    {
        return $this->regionRepository->findOneBy(['slug' => $slug]);
    }

    /**
     * @return Region[]
     */
    public function getAll()
    {
        return $this->regionRepository->findAll();
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getRegionQueryBuilder()
    {
        $qb = $this->regionRepository->createQueryBuilder('r')
            ->select('r');
        return $qb;
    }

    /**
     * @param $name
     * @param Region|null $ignoreRegion
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNameFree($name, Region $ignoreRegion = null)
    {
        $qb = $this->regionRepository->createQueryBuilder('r')
            ->select('r')
            ->where('r.name = :name')
            ->setParameters([
                'name' => $name,
            ]);

        if ($ignoreRegion) {
            $qb->andWhere('r != :ignoreRegion')
                ->setParameter('ignoreRegion', $ignoreRegion);
        }

        $query = $qb->getQuery();

        return (is_null($query->getOneOrNullResult()));
    }

    /**
     * @param Region $region
     * @param int $number
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function moveUp(Region $region, $number = 1)
    {
        if ($region->getPosition() > 0)
        {
            $region->setPosition($region->getPosition() -$number);
            $this->entityManager->persist($region);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Region $region
     * @param int $number
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function moveDown(Region $region, $number = 1)
    {
        $region->setPosition($region->getPosition() +$number);
        $this->entityManager->persist($region);
        $this->entityManager->flush();
    }
}