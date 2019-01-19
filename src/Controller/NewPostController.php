<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;



class NewPostController extends AbstractController
{
    /**
     * @Route("/new/post", name="new_post")
     */
    public function index(Request $request)
    {
        $post = new Post();
        $post -> setAuthor( $this->getUser() -> getName());
        $post -> setHeader('Write header');
        $post -> setBody('Write your post');
        $post -> setUser($this->getUser());
        $this -> getUser() -> addPost($post);


        $form = $this->createFormBuilder($post)
            ->add('header', TextType::class)
            ->add('body', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $post = $form->getData();
            // ... . выполните действия, такие как сохранение задачи в базе данных
            // например, если Task является сущностью Doctrine, сохраните его!
             $em = $this->getDoctrine()->getManager();
             $em->persist($post);
             $em->flush();

                      return $this->redirectToRoute('auth');
        }

        return $this->render('new_post/index.html.twig', [
            'newPost' => $form->createView(),
        ]);
    }
}
