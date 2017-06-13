<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Account;
use AppBundle\Entity\Category;
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
 * @Route("/admin")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="categories_list")
     */
    public function categoriesAction(Request $request)
    {
        $categories = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('admin/admin_categories.html.twig', [
            'categories' =>$categories,
            'operation' => new Operation()
        ]);
    }
}