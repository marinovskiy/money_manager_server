<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Entity\Operation;
use AppBundle\Entity\User;
use AppBundle\Form\AddNewsType;
use AppBundle\Form\Operation\AddOperationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/news")
 */
class NewsController extends Controller
{
    /**
     * @Route("/{id}/details", name="news_details")
     */
    public function newsDetailsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository(News::class)->find($id);
        if (!$news) {
            return new Response("Not found", 404);
        }
        if (!$news->getEnabled() && $this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response("Not found", 404);
        }

        $comments = $em->getRepository(Comment::class)->findBy(['news' => $id], ['createdAt' => 'DESC']);

        return $this->render('news/news_details.html.twig', ['news' => $news, 'comments' => $comments]);
    }
}