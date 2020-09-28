<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Comment;
use App\Service\Pagination;
use App\Form\AdminBookType;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookController extends AbstractController
{
    /**
     * Affiche la liste des reservations
     * @Route("/admin/books/{page<\d+>?1}", name="admin_books_list")
     * @return Response
     */
    public function index(BookRepository $repo,Pagination $paginationService,$page)
    {
        $paginationService->setEntityClass(Book::class)
                            ->setPage($page)
                            //->setRoute('admin_books_list')
                            ;

        return $this->render('admin/book/index.html.twig', [
            'pagination' => $paginationService
        ]);
    }

    /**
     * Edition d une reservation
     * @Route("/admin/book/{id}/edit",name="admin_book_edit")
     * @param Book $book
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Book $book,Request $request,ObjectManager $manager){

        $form = $this->createForm(AdminBookType::class,$book);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
                        
            $manager->persist($book);
            $manager->flush();

            $this->addFlash("success","La réservation a bien été modifiée");
        }

        return $this->render('admin/book/edit.html.twig',['book'=>$book,'form'=>$form->createView()]);

    }


    /**
     * Suppression d une reservation via admin
     * @Route("/admin/book/{id}/delete",name="admin_book_delete")
     * @param Book $book
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Book $book,ObjectManager $manager){

        $manager->remove($book);
        $manager->flush();

        $this->addFlash('success',"Réservation supprimée avec succès.");

        return $this->redirectToRoute('admin_books_list');

    }

}
