<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $userId = null !== $this->getUser() ? $this->getUser()->getId() : null;
        if(!$userId){
            $interactTag = " <span class=\"subheading\"> <a href=\"/register\">Sign up </a> or <a href=\"\">login </a>  to interact with this user</span>";
        } elseif ($userId==$user->getId()){
            $interactTag = 'edit';
        } else {
            $currentUser = $this->getUser();
            if(in_array($user, $currentUser->getFollowing()->toArray()))
                $interactTag = '<a class="btn btn-primary"
                               href="#" id="follow_options">Unsubscribe</a>';
            else{
                $interactTag = '<a class="btn btn-primary"
                               href="#" id="follow_options">Subscribe</a>';
            }
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'posts' => $user -> getPosts(),
            'interact_tag' => $interactTag
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('image')->getData();
            $fileName = $this->generateUniqueFileName().'.jpg';

            // Move the file to the directory where brochures are stored
            try {

                $file->move(
                    $this->getParameter('user_photo_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                throw new Exception('you loh');
            }
            $user->setImage($fileName);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

}
