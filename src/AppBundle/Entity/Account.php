<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountRepository")
 */
class Account implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"group1", "group2"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"group1", "group2"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Groups({"group1", "group2"})
     */
    private $description;

    /**
     * @var Currency
     *
     * @ORM\ManyToOne(targetEntity="Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * @Groups({"group1", "group2"})
     */
    private $currency;

    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2)
     * @Groups({"group1", "group2"})
     */
    private $balance;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="accounts")
     * @Groups({"group1", "group2"})
     */
    private $user;

    /**
     * @var ArrayCollection|$operations[]
     *
     * @ORM\OneToMany(targetEntity="Operation", mappedBy="account", cascade={"remove","persist"})
     * @Groups({"group1", "group2"})
     */
    private $operations;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="accounts")
     * @Groups({"group2"})
     */
    private $organization;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Groups({"group1", "group2"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Groups({"group1", "group2"})
     */
    private $updatedAt;

    public function __construct()
    {
        $this->operations = new ArrayCollection();
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
     * @return Account
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
     * Set description
     *
     * @param string $description
     *
     * @return Account
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Set balance
     *
     * @param float $balance
     *
     * @return Account
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Account
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add operation
     *
     * @param Operation $operation
     *
     * @return Account
     */
    public function addOperation(Operation $operation)
    {
        $this->operations->add($operation);

        return $this;
    }

    /**
     * Remove operation
     *
     * @param Operation $operation
     */
    public function removeOperation(Operation $operation)
    {
        $this->operations->removeElement($operation);
    }

    /**
     * Get operations
     *
     * @return ArrayCollection|$operations[]
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * @param ArrayCollection|$operations[] $operations
     */
    public function setOperations($operations)
    {
        $this->operations = $operations;
    }

    /**
     * Set organization
     *
     * @param Organization $organization
     * @return Account
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Account
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Account
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    function jsonSerialize()
    {
        if ($this->getUser() != null) {
            return [
                'id' => $this->getId(),
                'name' => $this->getName(),
                'description' => $this->getDescription(),
                'currency' => $this->getCurrency(),
//            'currency' => $this->getCurrency()->getId(),
                'balance' => $this->getBalance(),
                'user' => $this->getUser()->getId(),
                'operations' => $this->getOperations(),
                'createdAt' => $this->getCreatedAt(),
                'updatedAt' => $this->getUpdatedAt()
            ];
        } else if ($this->getOrganization() != null) {
            return [
                'id' => $this->getId(),
                'name' => $this->getName(),
                'description' => $this->getDescription(),
                'currency' => $this->getCurrency(),
//            'currency' => $this->getCurrency()->getId(),
                'balance' => $this->getBalance(),
                'operations' => $this->getOperations(),
                'organization' => $this->getOrganization()->getId(),
                'createdAt' => $this->getCreatedAt(),
                'updatedAt' => $this->getUpdatedAt()
            ];
        }
    }

    function __toString()
    {
        return $this->getName();
    }
}