<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Category;
use AppBundle\Entity\Currency;
use AppBundle\Entity\Feedback;
use AppBundle\Form\AddFeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/feedback")
 */
class ApiFeedbackController extends Controller
{
    /**
     * @Route("/", name="api_feedbackList")
     */
    public function apiFeedbackListAction(Request $request)
    {
        $feedbackList = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Feedback::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->json(['feedbackList' => $feedbackList], 200);
    }

//    /**
//     * @Route("/add", name="api_feedback_add")
//     * @Method("POST")
//     */
//    public function apiFeedbackAddAction(Request $request)
//    {
//        $data = json_decode($request->getContent(), true);
//        $feedbackText = $data['feedbackText'];
//
//        if ($feedbackText == '') {
//            return $this->json('Ви не можете залишити пустий відгук', 400);
//        }
//
//        $feedback = new Feedback();
//        $feedback->setText($feedbackText);
//        $feedback->setAuthor($this->getUser());
//        $feedback->setEnabled(true);
//        $feedback->setCreatedAt(new \DateTime());
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($feedback);
//        $em->flush();
//
//        return $this->json(['feedback' => $feedback], 200);
//    }

    /**
     * @Route("/add", name="api_feedback_add")
     * @Method("POST")
     */
    public function apiFeedbackAddAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $feedback = new Feedback();
        $form = $this->createForm(AddFeedbackType::class, $feedback);
        $form->submit($data['feedback']);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback->setAuthor($this->getUser());
            $feedback->setEnabled(true);
            $feedback->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            return $this->json(['msg' => 'success'], 200);
        }

        return $this->json('Invalid data', 400);
    }
}