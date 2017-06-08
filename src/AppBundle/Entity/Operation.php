<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Operation
 *
 * @ORM\Table(name="operation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperationRepository")
 */
class Operation implements \JsonSerializable
{
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    const TYPES_TITLES = ['Надходження' => self::TYPE_INCOME, 'Витрати' => self::TYPE_EXPENSE];

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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="sum", type="decimal", precision=10, scale=2)
     */
    private $sum;

//    /**
//     * @var datetime
//     *
//     * @ORM\Column(name="sum", type="datetime")
//     */
//    private $createdAt;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="operations")
     */
    protected $account;

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
     * @return string
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Operation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Operation
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
     * Set sum
     *
     * @param float $sum
     *
     * @return Operation
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Get sum
     *
     * @return float
     */
    public function getSum()
    {
        return $this->sum;
    }

//    /**
//     * @return mixed
//     */
//    public function getCreatedAt()
//    {
//        return $this->createdAt;
//    }
//
//    /**
//     * @param mixed $createdAt
//     */
//    public function setCreatedAt($createdAt)
//    {
//        $this->createdAt = $createdAt;
//    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Operation
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account = null)
    {
        $this->account = $account;
    }

    function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'description' => $this->getDescription(),
            'sum' => $this->getSum(),
            'category' => $this->getCategory()->getName(),
            'accountId' => $this->getAccount()->getId()
        ];
    }
}