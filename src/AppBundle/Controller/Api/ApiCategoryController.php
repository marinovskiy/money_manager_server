<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Category;
use AppBundle\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/categories")
 */
class ApiCategoryController extends Controller
{
    /**
     * @Route("", name="api_categories_all")
     * @Method({"GET"})
     */
    public function apiAllCategoriesAction()
    {
        $categories = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Category::class)
            ->findAll();

        return $this->json(['categories' => $categories], 200);
    }

    /**
     * @Route("/all", name="test_all")
     */
    public function apiAllAction()
    {
        $categories = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Category::class)
            ->findAll();

        $currencies = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Currency::class)
            ->findAll();

        return $this->json(['data' => ['categories' => $categories, 'currencies' => $currencies]], 200);
    }
}