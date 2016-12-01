<?php

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class City
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationCity")
 */
class City extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,unique=true,nullable=false)
     */
    private $name;

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
     * City constructor.
     * @param string $name
     * @param Country $country
     */
    public function __construct(Country $country, $name)
    {
        $this->name = $name;
        $this->country = $country;

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
}