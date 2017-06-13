<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Entity\Operation;
use AppBundle\Entity\Report;
use AppBundle\Form\Account\AccountReportType;
use AppBundle\Form\AddNewsType;
use AppBundle\Form\Operation\AddOperationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class ReportsController extends Controller
{
//    /**
//     * @Route("/{id}/comments/add", name="comment_add")
//     * @Method("GET")
//     */
//    public function commentAddAction(Request $request, $id)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $categories = $em->getRepository(Category::class)->findAll();
//
//        $report = new Report();
//        $form = $this->createForm(new AccountReportType($categories), $report);
//
//        return $this->render('report/reports.html.twig', array('form' => $form->createView()));
//    }

    /**
     * @Route("report", name="report")
     * @Method("GET")
     */
    public function reportAction(Request $request)
    {
        $accountsId = $request->query->get('accountId');

        $em = $this->getDoctrine()->getManager();
        $account = $em->getRepository(Account::class)->find($accountsId);

        return $this->render('report/reports.html.twig', array(
            'account' => $account
        ));
    }
}