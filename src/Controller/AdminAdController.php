<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Service\Pagination;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads\{page<\d+>?1}", name="admin_ads_list")
     */
    public function index(AdRepository $repo,$page,Pagination $paginationService)
    {
        $paginationService->setEntityClass(Ad::class)
                        ->setPage($page)
                        //->setRoute('admin_ads_list')
                        ;

        return $this->render('admin/ad/index.html.twig', [
            'pagination'=>$paginationService

        ]);
    }

        /**
     * permet de creer une annonce
     * @Route("/admin/ads/new",name="ads_create")
     * @return response
     */

    public function create(Request $request,ObjectManager $manager){

        // fabrcant de formulaire : FORMBUILDER

        $ad = new Ad();

        // on lance la fabrication et la configuration de notre formulaire

        $form = $this->createForm(AnnonceType::class,$ad);

        // recuperation des données du formulaire
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // si le fomulaire est soumis Et si le formulaire est valide, on demande à doctrine de sauvegarder ces données dans l'objet manager( dans la bdd)
            //pour chaque image supplémentaire ajoutée

            foreach($ad->getImages() as $image){

                // on relie l'image à l'annonce et on modifie l'annonce
                $image->setAd($ad);

                // on sauvegarde les images

                $manager->persist($image);
            }

           
            $manager ->persist($ad);
            $manager->flush();

            $this->addFlash('success',"Annonce <strong>{$ad->getTitle()}</strong> créée avec succès");

            return $this->redirectToRoute('admin_ads_list',['slug'=>$ad->getSlug()]);
        }

        return $this->render('admin/ad/new.html.twig',['form'=>$form->createView()]);

    }

    /**
     * Permet de modifier une annonce dans la partie admin
     * @Route("admin/ads/{id}/edit",name="admin_ads_edit")
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Ad $ad,Request $request,ObjectManager $manager){

        $form = $this->createForm(AnnonceType::class,$ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success',"L'annonce a bien été modifiée");

        }

        return $this->render('admin/ad/edit.html.twig',[
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);

    }

        /**
         * Suppression d une annonce
         * @Route("/admin/ads/{id}/delete",name="admin_ads_delete")
         * @param Ad $ad
         * @param ObjectManager $manager
         * @return Response
         */
    public function delete(Ad $ad,ObjectManager $manager){

        if(count($ad->getBookings()) > 0){

            $this->addFlash("warning","Vous ne pouvez pas supprimer une annonce qui contient des réservations.");
        }else{

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash('success',"Le plat <strong>{$ad->getTitle()}</strong> a bien été supprimée.");

    }

        return $this->redirectToRoute("admin_ads_list");

    }
}
