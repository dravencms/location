<?php declare(strict_types = 1);

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Nette;

/**
 * Class ZipCode
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationZipCode", uniqueConstraints={@UniqueConstraint(name="name_city_id", columns={"name", "city_id"})})
 */
class ZipCode
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="City", inversedBy="zipCodes")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @var ArrayCollection|Street[]
     * @ORM\OneToMany(targetEntity="Street", mappedBy="zipCode",cascade={"persist"})
     */
    private $streets;

    /**
     * ZipCode constructor.
     * @param City $city
     * @param string $name
     */
    public function __construct(City $city, string $name)
    {
        $this->city = $city;
        $this->name = $name;

        $this->streets = new ArrayCollection();
    }

    /**
     * @param City $city
     */
    public function setCity(City $city): void
    {
        $this->city = $city;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Street[]|ArrayCollection
     */
    public function getStreets(): Collection
    {
        return $this->streets;
    }
}