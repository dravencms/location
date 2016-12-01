<?php

namespace Dravencms\Model\Location\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Dravencms\Model\User\Entities\Company;
use Dravencms\Model\User\Entities\User;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class StreetNumber
 * @package App\Model\Entities
 * @ORM\Entity
 * @ORM\Table(name="locationStreetNumber")
 */
class StreetNumber extends Nette\Object
{
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
     * @ORM\Column(type="string",length=255,unique=true,nullable=false)
     */
    private $name;

    /**
     * @var ArrayCollection|Company[]
     * @ORM\OneToMany(targetEntity="Company", mappedBy="streetNumber",cascade={"persist"})
     */
    private $companies;

    /**
     * @var ArrayCollection|User[]
     * @ORM\OneToMany(targetEntity="User", mappedBy="streetNumber",cascade={"persist"})
     */
    private $users;

    /**
     * StreetNumber constructor.
     * @param Street $street
     * @param string $name
     */
    public function __construct(Street $street, $name)
    {
        $this->street = $street;
        $this->name = $name;
    }

    /**
     * @param Street $street
     */
    public function setStreet(Street $street)
    {
        $this->street = $street;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Company[]|ArrayCollection
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }
}