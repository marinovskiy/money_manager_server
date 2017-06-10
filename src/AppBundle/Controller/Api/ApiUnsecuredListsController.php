<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Category;
use AppBundle\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api")
 */
class ApiUnsecuredListsController extends Controller
{

    /**
     * @Route("/unsecuredLists", name="api_unsecured_lists")
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

        return $this->json(['categories' => $categories, 'currencies' => $currencies], 200);
    }
}