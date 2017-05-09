<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/9/17
 * Time: 5:46 PM
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserController extends Controller
{
    /**
     * @Route("/profile/me", name="profile_me")
     */
    public function profileMeAction(Request $request)
    {
        return $this->render('user/profile.html.twig');
    }
}