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
     * @param Region $region
     * @return Region[]
     */
    private function buildParentTreeResolver(Region $region)
    {
        $breadcrumb = [];

        $breadcrumb[] = $region;

        if ($region->getParent()) {
            foreach ($this->buildParentTreeResolver($region->getParent()) AS $sub) {
                $breadcrumb[] = $sub;
            }
        }
        return $breadcrumb;
    }

    /**
     * @param Region $region
     * @return Region[]
     */
    public function buildParentTree(Region $region)
    {
        return array_reverse($this->buildParentTreeResolver($region));
    }

    /**
     * @param $options
     * @return mixed
     */
    public function getTree($options)
    {
        $query = $this->regionRepository
            ->createQueryBuilder('node')
            ->select('node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->where('node.isHidden = :isHidden')
            ->andWhere('node.isActive = :isActive')
            ->setParameters(
                [
                    'isActive' => true
                ]
            )
            ->getQuery();

        return $this->regionRepository->buildTree($query->getArrayResult(), $options);
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
     * @param $name
     * @param Region|null $parentRegion
     * @param Region|null $ignoreRegion
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNameFree($name, Region $parentRegion = null, Region $ignoreRegion = null)
    {
        $qb = $this->regionRepository->createQueryBuilder('c')
            ->select('c')
            ->where('c.name = :name')
            ->setParameters([
                'name' => $name,
            ]);

        if ($parentRegion) {
            $qb->andWhere('c.parent = :parent')
                ->setParameter('parent', $parentRegion);
        } else {
            $qb->andWhere('c.parent IS NULL');
        }

        if ($ignoreRegion) {
            $qb->andWhere('c != :ignoreRegion')
                ->setParameter('ignoreRegion', $ignoreRegion);
        }

        $query = $qb->getQuery();

        return (is_null($query->getOneOrNullResult()));
    }

    /**
     * @param Region|null $parentRegion
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getRegionItemsQueryBuilder(Region $parentRegion = null)
    {
        $qb = $this->regionRepository->createQueryBuilder('m')
            ->select('m');

        if ($parentRegion) {
            $qb->andWhere('m.parent = :parent')
                ->setParameter('parent', $parentRegion);
        } else {
            $qb->andWhere('m.parent IS NULL');
        }

        $qb->orderBy('m.root, m.lft', 'ASC');

        return $qb;
    }

    /**
     * @param Region|null $parentRegion
     * @return Region[]
     */
    public function getByParent(Region $parentRegion = null)
    {
        return $this->regionRepository->findBy(['parent' => $parentRegion]);
    }

    /**
     * @param Region $child
     * @param Region $root
     */
    public function persistAsLastChildOf(Region $child, Region $root)
    {
        $this->regionRepository->persistAsLastChildOf($child, $root);
    }

    /**
     * @param Region $region
     * @param int $number
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function moveUp(Region $region, $number = 1)
    {
        if ($region->getParent())
        {
            //Use standard moveUp when item has parent
            $this->regionRepository->moveUp($region, $number);
        }
        else
        {
            if ($number != 1)
            {
                throw new \Exception('$number != 1 is not supported');
            }

            /** @var Region $prevItem */
            $prevItem = $this->regionRepository->createQueryBuilder('node')
                ->select('node')
                ->where('node.root < :root')
                ->orderBy('node.root', 'DESC')
                ->setParameter('root', $region->getRoot())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            
            if ($prevItem)
            {
                $prevItemRoot = $prevItem->getRoot();

                $qb = $this->regionRepository->createQueryBuilder('node');
                $qb->update()
                    ->set('node.root', $qb->expr()->literal($region->getRoot()))
                    ->where('node = :prevItem')
                    ->setParameter('prevItem', $prevItem)
                    ->getQuery()
                    ->execute();

                $qb = $this->regionRepository->createQueryBuilder('node');
                $qb->update()
                    ->set('node.root', $qb->expr()->literal($prevItemRoot))
                    ->where('node = :prevItem')
                    ->setParameter('prevItem', $region)
                    ->getQuery()
                    ->execute();
            }
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
        if ($region->getParent())
        {
            //Use standard moveUp when item has parent
            $this->regionRepository->moveDown($region, $number);
        }
        else
        {
            if ($number != 1)
            {
                throw new \Exception('$number != 1 is not supported');
            }

            /** @var Region $prevItem */
            $nextItem = $this->regionRepository->createQueryBuilder('node')
                ->select('node')
                ->where('node.root > :root')
                ->orderBy('node.root', 'ASC')
                ->setParameter('root', $region->getRoot())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($nextItem)
            {
                $nextItemRoot = $nextItem->getRoot();
                $qb = $this->regionRepository->createQueryBuilder('node');
                $qb->update()
                    ->set('node.root', $qb->expr()->literal($region->getRoot()))
                    ->where('node = :nextItem')
                    ->setParameter('nextItem', $nextItem)
                    ->getQuery()
                    ->execute();

                $qb = $this->regionRepository->createQueryBuilder('node');
                $qb->update()
                    ->set('node.root', $qb->expr()->literal($nextItemRoot))
                    ->where('node = :nextItem')
                    ->setParameter('nextItem', $region)
                    ->getQuery()
                    ->execute();
            }
        }
    }

    /**
     * @param Region $region
     * @param bool $direct
     * @param null $orderBy
     * @return mixed
     */
    public function getChildren(Region $region, $direct = true, $orderBy = null)
    {
        return $this->regionRepository->children($region, $direct, $orderBy);
    }
}