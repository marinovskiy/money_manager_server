<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Currency;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/currencies")
 */
class ApiCurrencyController extends Controller
{
    /**
     * @Route("", name="api_currencies_all")
     * @Method({"GET"})
     */
    public function apiAllCurrenciesAction()
    {
        $currencies = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Currency::class)
            ->findAll();

        return $this->json(['currencies' => $currencies], 200);
    }
}