<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Service\Pagination;
use App\Form\AdminCommentType;
use App\Repository\CommentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_list")
     */
    public function index(CommentsRepository $repo,$page,Pagination $paginationService)
    {
        $paginationService->setEntityClass(Comments::class)
                            ->setLimit(5)
                            ->setPage($page)
                            //->setRoute('admin_comments_list')
                            ;

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $paginationService
        ]);
    }

    /**
     * Permet d editer un commentaire via l admin
     * @Route("/admin/comment/{id}/edit",name="admin_comment_edit")
     * @param Comment $comment
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Comments $comments,Request $request,ObjectManager $manager){

        $form = $this->createForm(AdminCommentType::class,$comments);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success","Le commentaire a été enregistré");
            return $this->redirectToRoute('admin_comments_list');

        }

        return $this->render('admin/comment/edit.html.twig',[
            'comments'=>$comments,
            'form'=>$form->createView()
        ]);

    }

    /**
     * Suppression d un commentaire via admin
     * @Route("/admin/comment/{id}/delete",name="admin_comment_delete")
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Comments $comments,ObjectManager $manager){

        $manager->remove($comments);
        $manager->flush();

        $this->addFlash('success',"Le commentaire {$comments->getId()} a bien été supprimé.");

        return $this->redirectToRoute('admin_comments_list');

    }
}
