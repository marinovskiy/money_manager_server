<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Comment;
use AppBundle\Entity\News;
use AppBundle\Form\AddNewsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/main", name="admin_main_page")
     */
    public function adminMainAction(Request $request)
    {
        return new Response('<html><body>Admin page!</body></html>');
    }

    /**
     * @Route("/news/add", name="news_add")
     */
    public function addNewsAction(Request $request)
    {
        $news = new News();
        $form = $this->createForm(AddNewsType::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setCreatedAt(new \DateTime());
            $news->setUpdatedAt(new \DateTime());
            $news->setAuthor($this->getUser());
            $news->setEnabled(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($news);
            $em->flush();

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('news/add_news.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/news/{id}/edit", name="news_edit")
     */
    public function editNewsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository(News::class)->find($id);
        $form = $this->createForm(AddNewsType::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setUpdatedAt(new \DateTime());
            $em->flush();
            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('news/add_news.html.twig', ['form' => $form->createView(), 'news' => $news]);
    }

    /**
     * @Route("/news/{id}/hide", name="news_hide")
     */
    public function hideNewsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository(News::class)->find($id);

        if ($news && $news->getEnabled()) {
            $news->setEnabled(false);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('admin_newses');
        }

        return $this->redirectToRoute('admin_newses');
    }

    /**
     * @Route("/news/{id}/show", name="news_show")
     */
    public function showNewsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository(News::class)->find($id);

        if ($news && !$news->getEnabled()) {
            $news->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('admin_newses');
        }

        return $this->redirectToRoute('admin_newses');
    }

    /**
     * @Route("/news/{id}/comments/{commentId}/delete", name="news_delete_comment")
     */
    public function deleteNewsComment(Request $request, $id, $commentId)
    {
        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository(News::class)->find($id);
        if (!$news) {
            return new Response("News not found", 404);
        }

        $comment = $em->getRepository(Comment::class)->find($commentId);
        if (!$comment) {
            return new Response("Comment not found", 404);
        }

        if (!$news->getComments()->contains($comment)) {
            return new Response("This news does not contain this comment", 404);
        }

        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('news_details', ['id' => $id]);
    }
}