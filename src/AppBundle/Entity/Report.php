<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Report
{
    const TYPE_ALL = 'all';
    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    const TYPE_TITLES = ['Усі' => self::TYPE_ALL, 'Надходження' => self::TYPE_INCOME, 'Витрати' => self::TYPE_EXPENSE];

    private $type;

    private $category;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
}