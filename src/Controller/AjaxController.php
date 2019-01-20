<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax", name="ajax")
     */
    public function AjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();
            $follower_id = $request->request->get('follower_id');
            $follower = $entityManager->getRepository('App:User')->find($follower_id);
            $user_for_follow_id = $request->request->get('user_for_follow_id');
            $user_for_follow = $entityManager->getRepository('App:User')->find($user_for_follow_id);
            if ($follower->getFollowing()->contains($user_for_follow)) {
                $follower->removeFollowing($user_for_follow);
                $entityManager->flush();
                return new Response('remove', 200);
            } else {
                $follower->addFollowing($user_for_follow);
                $entityManager->flush();
                return new Response('add', 200);
            }
        } else {
            return new Response('This is not ajax!', 400);
        }
    }
}