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
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/feedback")
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
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->render('feedback/feedback_details.html.twig', array(
            'feedbackList' => $feedbackList
        ));
    }

    /**
     * @Route("/add", name="feedback_add")
     * @Method("POST")
     */
    public function feedbackAddAction(Request $request)
    {
        $feedbackText = $request->request->get('feedback');

        if ($feedbackText == '') {
            return new Response('Ви не можете залишити пустий відгук', 400);
        }

        $feedback = new Feedback();
        $feedback->setText($feedbackText);
        $feedback->setAuthor($this->getUser());
        $feedback->setEnabled(true);
        $feedback->setCreatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($feedback);
        $em->flush();

        return new Response('success', 200);
    }
}