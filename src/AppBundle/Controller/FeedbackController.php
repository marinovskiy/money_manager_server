<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Form\UpdateProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/feedbacks")
 */
class FeedbackController extends Controller
{
    /**
     * @Route("/", name="feedbackList")
     */
    public function feedbackListAction(Request $request)
    {
        $feedbackList = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Feedback::class)
            ->findAll();

        return $this->render('feedback/feedback_details.html.twig', array(
            'feedbackList' => $feedbackList
        ));
    }

//    /**
//     * @Route("/add", name="feedback_add")
//     */
//    public function feedbackAddAction(Request $request)
//    {
//
//
//        return $this->render('feedback/feedback_details.html.twig', array(
//            'feedbackList' => $feedbackList
//        ));
//    }
}