<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Entity\Operation;
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
class CommentController extends Controller
{
    /**
     * @Route("/{id}/comments/add", name="comment_add")
     * @Method("POST")
     */
    public function commentAddAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository(News::class)->find($id);
        if (!$news) {
            return $this->json('News not found', 404);
        }
        if (!$news->getEnabled()) {
            return $this->json('News not found', 404);
        }

        $commentText = $request->request->get('comment');

        if ($commentText == '') {
            return new Response('Ви не можете залишити пустий коментар', 400);
        }

        $comment = new Comment();
        $comment->setNews($news);
        $comment->setText($commentText);
        $comment->setAuthor($this->getUser());
        $comment->setCreatedAt(new \DateTime());

        $em->persist($comment);
        $em->flush();

        return new Response('success', 200);
    }
}