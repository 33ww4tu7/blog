<?php


namespace App\Controller;


use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/authorized", name="auth")
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('auth.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }
}