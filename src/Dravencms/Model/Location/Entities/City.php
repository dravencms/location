<?php

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Nette;

/**
 * Class City
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationCity", uniqueConstraints={@UniqueConstraint(name="name_country_id", columns={"name", "country_id"})})
 */
class City extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255, unique=true,nullable=false)
     */
    private $slug;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="cities")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    private $country;

    /**
     * @var ArrayCollection|ZipCode[]
     * @ORM\OneToMany(targetEntity="ZipCode", mappedBy="city",cascade={"persist"})
     */
    private $zipCodes;

    /**
     * @var Region
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="cities")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", nullable=true)
     */
    private $region;

    /**
     * City constructor.
     * @param string $name
     * @param Country $country
     */
    public function __construct(Country $country, $name, Region $region = null)
    {
        $this->name = $name;
        $this->country = $country;
        $this->region = $region;

        $this->zipCodes = new ArrayCollection();
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return ZipCode[]|ArrayCollection
     */
    public function getZipCodes()
    {
        return $this->zipCodes;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

}