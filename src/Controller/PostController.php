<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping\Id;
use phpDocumentor\Reflection\Types\String_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



class PostController extends AbstractController
{
    private $posts;

    public function __construct(PostRepository $posts)
    {
        $this->posts=$posts;
    }
    /**
     * @Route("/authorized", name="post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('auth.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $post -> setAuthor( $this->getUser() -> getName());
        $post -> setUser($this->getUser());

        $form = $this->createFormBuilder($post)
            ->add('header', TextType::class)
            ->add('body', TextType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'posts' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}/{user_id}", name="post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'user' => $post->getUser()
        ]);
    }

    /**
     * @Route("/edit/{id}/{user_id}", name="post_edit", methods={"GET","POST"})
     *
     * @IsGranted("ROLE_BLOGGER")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createFormBuilder($post)
            ->add('header', TextType::class)
            ->add('body', TextType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index', [
                'id' => $post->getId(),
                'user' => $post->getUser()
            ]);
        }



        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="post_delete", methods={"DELETE"})
     * @isGranted("ROLE_ADMIN", statusCode=403, message="Access denied")
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index');
    }
}
