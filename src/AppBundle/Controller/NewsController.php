<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\News;
use AppBundle\Entity\Operation;
use AppBundle\Form\AddNewsType;
use AppBundle\Form\Operation\AddOperationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/news")
 */
class NewsController extends Controller
{
    /**
     * @Route("/add", name="news_add")
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
     * @Route("/{id}/edit", name="news_edit")
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
     * @Route("{id}/hide", name="news_hide")
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
     * @Route("{id}/show", name="news_show")
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
}