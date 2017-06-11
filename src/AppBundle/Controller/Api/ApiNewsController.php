<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/news")
 */
class ApiNewsController extends Controller
{
    /**
     * @Route("/list", name="api_newsList")
     */
    public function apiNewsListAction(Request $request)
    {
        $newsList = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(News::class)
            ->findBy([], ['createdAt' => 'DESC']);

        return $this->json(['newsList' => $newsList], 200);
    }

    /**
     * @Route("/{id}/details", name="api_news_details")
     */
    public function apiNewsDetailsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository(News::class)->find($id);
        if (!$news) {
            return $this->json("Not found", 404);
        }
        if (!$news->getEnabled()) {
            return $this->json("Not found", 404);
        }

//        $comments = $em->getRepository(Comment::class)->findBy(['news' => $id], ['createdAt' => 'DESC']);

        return $this->json(['news' => $news], 200);
    }
}