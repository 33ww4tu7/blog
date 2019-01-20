<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 20.01.2019
 * Time: 21:06
 */

namespace App\Controller;


use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreferencesController extends AbstractController
{
    /**
     * @Route("/preferences", name="pref")
     */
    public function index(PostRepository $postRepository): Response
    {
        $user = $this->getUser();
        $preferences_users = $user->getFollowing();
        $posts = [];
        foreach ($preferences_users as $preference_user){
            echo "<script>console.log( 'Debug Objects: " . $preference_user->getId() . "' );</script>";
            $posts = array_merge($posts, $postRepository->findByUserId($preference_user->getId()));
        }
        return $this->render('/post/pref.html.twig', [
            'posts' => $posts,
        ]);
    }
}