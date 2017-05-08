<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 9:49 AM
 */

namespace AppBundle\Manager;

use AppBundle\Entity\Expense;

class ExpenseManager
{
    public function addExpense(Expense $expense)
    {
        $expense
            ->setCreatedAt()
            ->setUpdatedAt();
    }
}