<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LandingController extends AbstractController
{
    #[Route(path: '/', name: 'app_landing')]
    public function index(): Response
    {
        return $this->redirectToRoute('admin');
    }
}
