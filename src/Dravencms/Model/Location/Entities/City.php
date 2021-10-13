<?php declare(strict_types = 1);

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use Nette;

/**
 * Class City
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationCity", uniqueConstraints={@UniqueConstraint(name="name_country_id", columns={"name", "country_id"})})
 */
class City
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255, unique=true,nullable=true)
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
     * @param Country $country
     * @param string $name
     * @param Region|null $region
     */
    public function __construct(Country $country, string $name, Region $region = null)
    {
        $this->name = $name;
        $this->country = $country;
        $this->region = $region;

        $this->zipCodes = new ArrayCollection();
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region = null): void
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Country
     */
    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * @return ZipCode[]|ArrayCollection
     */
    public function getZipCodes(): ArrayCollection
    {
        return $this->zipCodes;
    }

    /**
     * @return Region
     */
    public function getRegion(): Region
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

}
