<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Account;
use AppBundle\Form\Account\AddAccountType;
use HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/new", name="api_accounts_add")
     * @Method({"POST"})
     */
    public function apiNewAccountAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $account = new Account();
        $form = $this->createForm(AddAccountType::class, $account);
        $form->submit($data['account']);

        $logger = $this->get('logger');
        $logger->info('I just got the logger');
        foreach ($form->getErrors() as $err) {
            $logger->info($err->getMessage());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $account->setUser($this->getUser());
            $account->setBalance(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

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
//        $accounts = $this
//            ->getDoctrine()
//            ->getManager()
//            ->getRepository(Account::class)
//            ->loadAllUserAccounts($this->getUser()->getId());
//        $accounts = $this->getDoctrine()->getManager()->getRepository(Account::class)->findAll();
        $accounts = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Account')
            ->findBy(['user' => $this->getUser()->getId()]);
        return $this->json(['accounts' => $accounts], 200);
    }

    /**
     * @Route("/{id}/edit", name="api_accounts_edit")
     * @Method({"PUT"})
     */
    public function apiEditAccountAction(Request $request, $id)
    {

    }

    /**
     * @Route("/{id}/delete", name="api_accounts_delete")
     * @Method({"DELETE"})
     */
    public function apiDeleteAccountAction(Request $request, $id)
    {

    }
}