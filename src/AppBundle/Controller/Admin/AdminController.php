<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Currency;
use AppBundle\Entity\News;
use AppBundle\Entity\User;
use AppBundle\Form\AddCategoryType;
use AppBundle\Form\AddCurrencyType;
use AppBundle\Form\AddNewsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

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
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

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
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

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
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

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
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

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

    /**
     * @Route("/categories/add", name="categories_add")
     */
    public function addCategoryAction(Request $request)
    {
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

        $category = new Category();
        $form = $this->createForm(AddCategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirect($this->generateUrl('categories_list'));
        }

        return $this->render('admin/add_category.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/categories/{id}/edit", name="categories_edit")
     */
    public function editCategoryAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($id);
        $form = $this->createForm(AddCategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('categories_list'));
        }

        return $this->render('admin/edit_category.html.twig', ['form' => $form->createView(), 'category' => $category]);
    }

    /**
     * @Route("/categories/{id}/hide", name="categories_hide")
     */
    public function hideCategoryAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($id);

        if ($category && $category->getEnabled()) {
            $category->setEnabled(false);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('categories_list');
        }

        return $this->redirectToRoute('categories_list');
    }

    /**
     * @Route("/categories/{id}/show", name="categories_show")
     */
    public function showCategoryAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($id);

        if ($category && !$category->getEnabled()) {
            $category->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('categories_list');
        }

        return $this->redirectToRoute('categories_list');
    }

    /**
     * @Route("/currencies/add", name="currencies_add")
     */
    public function addCurrencyAction(Request $request)
    {
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

        $currency = new Currency();
        $form = $this->createForm(AddCurrencyType::class, $currency);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $currency->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($currency);
            $em->flush();

            return $this->redirect($this->generateUrl('currencies_list'));
        }

        return $this->render('admin/add_currency.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/currencies/{id}/edit", name="currencies_edit")
     */
    public function editCurrencyAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

        $em = $this->getDoctrine()->getManager();

        $currency = $em->getRepository(Currency::class)->find($id);
        $form = $this->createForm(AddCurrencyType::class, $currency);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('currencies_list'));
        }

        return $this->render('admin/edit_currency.html.twig', ['form' => $form->createView(), 'currency' => $currency]);
    }

    /**
     * @Route("/currencies/{id}/hide", name="currencies_hide")
     */
    public function hideCurrencyAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

        $em = $this->getDoctrine()->getManager();

        $currency = $em->getRepository(Currency::class)->find($id);

        if ($currency && $currency->getEnabled()) {
            $currency->setEnabled(false);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('currencies_list');
        }

        return $this->redirectToRoute('currencies_list');
    }

    /**
     * @Route("/currencies/{id}/show", name="currencies_show")
     */
    public function showCurrencyAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() != User::ROLE_ADMIN) {
            return new Response(null, 403);
        }

        $em = $this->getDoctrine()->getManager();

        $currency = $em->getRepository(Currency::class)->find($id);

        if ($currency && !$currency->getEnabled()) {
            $currency->setEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('currencies_list');
        }

        return $this->redirectToRoute('currencies_list');
    }
}