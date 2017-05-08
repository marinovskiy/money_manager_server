<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/8/17
 * Time: 10:02 AM
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoryData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $category1 = new Category();
        $category1->setName('Food');
        $category1->setType(Category::CATEGORY_TYPE_EXPENSE);

        $category2 = new Category();
        $category2->setName('Car');
        $category2->setType(Category::CATEGORY_TYPE_EXPENSE);

        $category3 = new Category();
        $category3->setName('Entertainment');
        $category3->setType(Category::CATEGORY_TYPE_EXPENSE);

        $category4 = new Category();
        $category4->setName('Salary');
        $category4->setType(Category::CATEGORY_TYPE_INCOME);

        $category5 = new Category();
        $category5->setName('Stipend');
        $category5->setType(Category::CATEGORY_TYPE_INCOME);

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);
        $manager->persist($category5);
        $manager->flush();
    }
}