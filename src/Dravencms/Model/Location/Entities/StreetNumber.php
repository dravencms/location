<?php declare(strict_types = 1);

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Nette;

/**
 * Class StreetNumber
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationStreetNumber", uniqueConstraints={@UniqueConstraint(name="name_street_id", columns={"name", "street_id"})})
 */
class StreetNumber
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var Street
     * @ORM\ManyToOne(targetEntity="Street", inversedBy="streetNumbers")
     * @ORM\JoinColumn(name="street_id", referencedColumnName="id")
     */
    private $street;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;


    /**
     * StreetNumber constructor.
     * @param Street $street
     * @param string $name
     */
    public function __construct(Street $street, string $name)
    {
        $this->street = $street;
        $this->name = $name;
    }

    /**
     * @param Street $street
     */
    public function setStreet(Street $street): void
    {
        $this->street = $street;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Street
     */
    public function getStreet(): Street
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}