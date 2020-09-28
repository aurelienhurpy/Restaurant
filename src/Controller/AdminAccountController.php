<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [

            'hasError'=>$error !==null,
            'username'=>$username
            
        ]);
    }

    /**
     * Permet la deconnexion de la partie admin
     * @Route("/admin/logout",name="admin_account_logout")
     * @return void
     */
    public function logout(){

    }
}
