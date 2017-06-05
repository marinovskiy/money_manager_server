<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Account;
use AppBundle\Form\Account\AddAccountType;
use HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/accounts")
 */
class ApiAccountController extends Controller
{
    /**
     * @Route("/new", name="api_account_add")
     * @Method({"POST"})
     */
    public function apiNewAccountAction(Request $request)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($request->getContent(), true);

        $account = new Account();
        $form = $this->createForm(AddAccountType::class, $account);
        $form->submit($data['account']);

        $logger = $this->get('logger');
        $logger->info('I just got the logger');
        foreach ($form->getErrors() as $err) {
            echo $err->getMessage();
            $logger->info($err->getMessage());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $account->setUser($this->getUser());
            $account->setBalance(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

//            $response = new Response(
//                $serializer->serialize(
//                    $account,
//                    'json'
//                ),
//                200
//            );
//            $response->headers->set('Content-Type', 'application/json');
//            return $response;
            return $this->json(['account' => $account], 200);
        }

        return $this->json('Invalid data', 400);
    }

    /**
     * @Route("/", name="api_accounts_all")
     * @Method({"GET"})
     */
    public function apiAllAccountsAction()
    {
        $userId = $this->getUser()->getId();

        $accounts = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Account::class)
            ->loadAllUserAccounts($userId);

        return $this->json(['accounts' => $accounts], 200);
    }
}