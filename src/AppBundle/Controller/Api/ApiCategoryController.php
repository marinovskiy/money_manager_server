<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Category;
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
}