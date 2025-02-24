<?php

namespace App\Controller;

use App\Service\TenantServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LandingController extends AbstractController
{
    #[Route(path: '/', name: 'app_landing')]
    public function index(TenantServiceInterface $tenantService): Response
    {
        return $this->render('landing.html.twig');
    }
}
