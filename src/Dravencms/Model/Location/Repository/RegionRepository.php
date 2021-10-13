<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Repository;

use Dravencms\Model\Location\Entities\Region;
use Dravencms\Database\EntityManager;

class RegionRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|Region|string */
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
     * @return null|Region
     */
    public function getOneById(int $id): ?Region
    {
        return $this->regionRepository->find($id);
    }

    /**
     * @param $slug
     * @return null|Region
     */
    public function getOneBySlug(string $slug): ?Region
    {
        return $this->regionRepository->findOneBy(['slug' => $slug]);
    }

    /**
     * @return Region[]
     */
    public function getAll(): array
    {
        return $this->regionRepository->findAll();
    }

    /**
     * @return mixed
     */
    public function getRegionQueryBuilder()
    {
        $qb = $this->regionRepository->createQueryBuilder('r')
            ->select('r');
        return $qb;
    }

    /**
     * @param string $name
     * @param Region|null $ignoreRegion
     * @return bool
     */
    public function isNameFree(string $name, Region $ignoreRegion = null): bool
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
     */
    public function moveUp(Region $region, int $number = 1): void
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
     */
    public function moveDown(Region $region, int $number = 1): void
    {
        $region->setPosition($region->getPosition() +$number);
        $this->entityManager->persist($region);
        $this->entityManager->flush();
    }
}