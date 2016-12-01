<?php

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class Country
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationCountry")
 */
class Country extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,unique=true,nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string",length=2,unique=true,nullable=false)
     */
    private $code;

    /**
     * @var ArrayCollection|City[]
     * @ORM\OneToMany(targetEntity="City", mappedBy="country",cascade={"persist"})
     */
    private $cities;

    /**
     * Country constructor.
     * @param string $name
     * @param string $code
     */
    public function __construct($name, $code)
    {
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return Country[]|ArrayCollection
     */
    public function getCities()
    {
        return $this->cities;
    }
}