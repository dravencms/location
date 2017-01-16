<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class Region
 * @ORM\Entity(repositoryClass="Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(name="locationRegion")
 */
class Region extends Nette\Object
{
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
    public function __construct($name, $isActive = true)
    {
        $this->name = $name;
        $this->isActive = $isActive;
        $this->cities = new ArrayCollection();
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param boolean $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return boolean
     */
    public function isIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param City $city
     */
    public function addCity(City $city)
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
    public function removeCity(City $city)
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
    public function setCities(ArrayCollection $cities)
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
     * @return \Doctrine\Common\Collections\Collection|\Dravencms\Model\Location\Entities\City[]
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}