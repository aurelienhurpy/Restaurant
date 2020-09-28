<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Comment;
use App\Service\Pagination;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Affiche la liste des reservations
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_list")
     * @return Response
     */
    public function index(BookingRepository $repo,Pagination $paginationService,$page)
    {
        $paginationService->setEntityClass(Booking::class)
                            ->setPage($page)
                            //->setRoute('admin_bookings_list')
                            ;

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $paginationService
        ]);
    }

    /**
     * Edition d une reservation
     * @Route("/admin/booking/{id}/edit",name="admin_booking_edit")
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Booking $booking,Request $request,ObjectManager $manager){

        $form = $this->createForm(AdminBookingType::class,$booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            //$booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());
            $booking->setAmount(0);
            
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash("success","La commande a bien été modifiée");
        }

        return $this->render('admin/booking/edit.html.twig',['booking'=>$booking,'form'=>$form->createView()]);

    }


    /**
     * Suppression d une reservation via admin
     * @Route("/admin/booking/{id}/delete",name="admin_booking_delete")
     * @param Booking $booking
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Booking $booking,ObjectManager $manager){

        $manager->remove($booking);
        $manager->flush();

        $this->addFlash('success',"Commande supprimée avec succès.");

        return $this->redirectToRoute('admin_bookings_list');

    }

}
