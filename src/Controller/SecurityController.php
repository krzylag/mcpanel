<?php

namespace App\Controller;

use App\Form\LoginFormType;
use App\Form\RegistrationFormType;
use App\Service\TenantServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginFormType::class);
        $form->handleRequest($request);

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'form' => $form,
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, TenantServiceInterface $tenantService): Response
    {
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $isCorrect = $tenantService->checkPassword(
                TenantEnum::from($data['tenant']),
                $data['password']
            );
            if (!$isCorrect) {
                $error = 'Wrong password';
            }
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form,
            'error' => $error,
        ]);
    }


}
