<?php declare(strict_types = 1);

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Dravencms\Database\Attributes\Identifier;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Nette;

/**
 * Class Street
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationStreet", uniqueConstraints={@UniqueConstraint(name="name_zip_code_id", columns={"name", "zip_code_id"})})
 */
class Street
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var ZipCode
     * @ORM\ManyToOne(targetEntity="ZipCode", inversedBy="streets")
     * @ORM\JoinColumn(name="zip_code_id", referencedColumnName="id")
     */
    private $zipCode;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=false)
     */
    private $name;
    
    /**
     * @var ArrayCollection|StreetNumber[]
     * @ORM\OneToMany(targetEntity="StreetNumber", mappedBy="street",cascade={"persist"})
     */
    private $streetNumbers;

    /**
     * Street constructor.
     * @param ZipCode $zipCode
     * @param string $name
     */
    public function __construct(ZipCode $zipCode, string $name)
    {
        $this->zipCode = $zipCode;
        $this->name = $name;
    }

    /**
     * @param ZipCode $zipCode
     */
    public function setZipCode(ZipCode $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return ZipCode
     */
    public function getZipCode(): ZipCode
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return StreetNumber[]|ArrayCollection
     */
    public function getStreetNumbers(): ArrayCollection
    {
        return $this->streetNumbers;
    }
}