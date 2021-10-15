<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Nette;

/**
 * Class Region
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(name="locationRegion")
 */
class Region
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255, unique=true,nullable=false)
     */
    private $slug;

    /**
     * @var ArrayCollection|City[]
     * @ORM\OneToMany(targetEntity="City", mappedBy="region",cascade={"persist"})
     */
    private $cities;

    /**
     * @var integer
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    private $position;


    /**
     * Category constructor.
     * @param string $name
     * @param bool $isActive
     */
    public function __construct(string $name, bool $isActive = true)
    {
        $this->name = $name;
        $this->isActive = $isActive;
        $this->cities = new ArrayCollection();
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param City $city
     */
    public function addCity(City $city): void
    {
        if ($this->cities->contains($city)) {
            return;
        }
        $this->cities->add($city);
        $city->setRegion($this);
    }

    /**
     * @param City $city
     */
    public function removeCity(City $city): void
    {
        if (!$this->cities->contains($city)) {
            return;
        }
        $this->cities->removeElement($city);
        $city->setRegion(null);
    }


    /**
     * @param ArrayCollection $cities
     */
    public function setCities(ArrayCollection $cities): void
    {
        //Remove all not in
        foreach($this->cities AS $city)
        {
            if (!$cities->contains($city))
            {
                $this->removeCity($city);
            }
        }
        //Add all new
        foreach($cities AS $city)
        {
            if (!$this->cities->contains($city))
            {
                $this->addCity($city);
            }
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}