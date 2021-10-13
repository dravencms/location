<?php declare(strict_types = 1);

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Nette;

/**
 * Class Country
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationCountry")
 */
class Country
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,unique=true,nullable=false)
     */
    private $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255, unique=true,nullable=true)
     */
    private $slug;

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
    public function __construct(string $name, string $code)
    {
        $this->name = $name;
        $this->code = $code;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return Country[]|ArrayCollection
     */
    public function getCities(): ArrayCollection
    {
        return $this->cities;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}