<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Form\AddCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/api/news")
 */
class ApiCommentsController extends Controller
{
    /**
     * @Route("/{id}/comments/new", name="api_news_comments_new")
     */
    public function apiNewsListAction(Request $request, $id)
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $news = $em->getRepository(News::class)->find($id);
//        if (!$news) {
//            return $this->json('News not found', 404);
//        }
//        if (!$news->getEnabled()) {
//            return $this->json('News not found', 404);
//        }
//
//        $commentText = $request->request->get('comment');
//
//        if ($commentText == '') {
//            return new Response('Ви не можете залишити пустий коментар', 400);
//        }
//
//        $comment = new Comment();
//        $comment->setNews($news);
//        $comment->setText($commentText);
//        $comment->setAuthor($this->getUser());
//        $comment->setCreatedAt(new \DateTime());
//
//        $em->persist($comment);
//        $em->flush();
//
//        return new Response('success', 200);

        $em = $this->getDoctrine()->getManager();
        $news = $em->getRepository(News::class)->find($id);
        if (!$news) {
            return $this->json(['msg' => 'news not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $comment = new Comment();
        $form = $this->createForm(AddCommentType::class, $comment);
        $form->submit($data['comment']);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setNews($news);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

//            return $this->json(['account' => $account], 200);
            return $this->json(['msg' => 'success'], 200);
        }

        return $this->json('Invalid data', 400);
    }
}