<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home/index')]
    public function index() : Response
    {
        $titre = "SYMFONYYYY";

        return $this->render("home/index.html.twig", compact("titre"));
    }
}