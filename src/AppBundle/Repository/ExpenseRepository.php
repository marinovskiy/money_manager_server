<?php

namespace AppBundle\Repository;

class ExpenseRepository extends \Doctrine\ORM\EntityRepository
{
    public function loadAllExpenses()
    {
        return $this->findAll();
    }
}