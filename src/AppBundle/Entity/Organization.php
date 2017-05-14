<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Organization
 *
 * @ORM\Table(name="organization")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrganizationRepository")
 */
class Organization
{
    const TYPE_FAMILY = 'family';
    const TYPE_COMPANY = 'company';
    const TYPE_ORGANIZATION = 'organization';

    const TYPES_TITLES = [
        'Family' => self::TYPE_FAMILY,
        'Company' => self::TYPE_COMPANY,
        'Organization' => self::TYPE_ORGANIZATION
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="publicAccess", type="boolean")
     */
    private $publicAccess;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="createdOrganizations")
     */
    private $creator;

    /**
     * @var ArrayCollection|$members[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="organizations")
     */
    private $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Organization
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function getPublicAccess()
    {
        return $this->publicAccess;
    }

    /**
     * @param boolean $publicAccess
     */
    public function setPublicAccess($publicAccess)
    {
        if (strcmp($this->type, self::TYPE_ORGANIZATION) !== 0) {
            throw new Exception('Only organizations can have public access');
        } else {
            $this->publicAccess = $publicAccess;
        }
    }

    /**
     * Set user
     *
     * @param User $creator
     * @return Organization
     */
    public function setUser(User $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return int
     */
    public function getUser()
    {
        return $this->creator;
    }

    /**
     * Add member
     *
     * @param User $member
     *
     * @return Organization
     */
    public function addMember(User $member)
    {
        $this->members->add($member);

        return $this;
    }

    /**
     * Remove member
     *
     * @param User $member
     */
    public function removeMember(User $member)
    {
        $this->members->removeElement($member);
    }

    /**
     * Get members
     *
     * @return ArrayCollection|$members[]
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param User[] $members
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }
}

