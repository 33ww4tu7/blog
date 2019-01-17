<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 16.01.2019
 * Time: 19:13
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}