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
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
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
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="parentRelationField", value="parent"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="/")
     *      })
     * }, fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255, unique=true,nullable=false)
     */
    private $slug;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Region", mappedBy="parent")
     */
    private $children;

    /**
     * @var ArrayCollection|City[]
     * @ORM\OneToMany(targetEntity="City", mappedBy="region",cascade={"persist"})
     */
    private $cities;


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
     * @param Region|null $parent
     * @return $this
     */
    public function setParent(Region $parent = null)
    {
        $this->parent = $parent;

        return $this;
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
     * @return Region
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Region
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return mixed
     */
    public function getLvl()
    {
        return $this->lvl;
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
     * @return mixed
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * @return mixed
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * @param mixed $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
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
}