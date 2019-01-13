<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index(CommentRepository $repo, $page, PaginationService $pagination)
    {
        //$repo = $this->getDoctrine()->getRepository(Comment::class);

        $pagination->setEntityClass(Comment::class)
                    ->setPage($page)
                    ->setLimit(5);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Modification
     * 
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * 
     * @return Response
     */
    public function edit(Comment $comment, ObjectManager $manager, Request $request)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Le commentaire a bien été modifié !');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Suppression
     * 
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return void
     */
    public function delete(Comment $comment, ObjectManager $manager)
    {
        $manager->remove($comment);
        
        $this->addFlash('success', "Le commentaire <strong>{$comment->getId()}</strong> a bien été supprimé !");

        $manager->flush();

        return $this->redirectToRoute('admin_comments_index');
    }
}
