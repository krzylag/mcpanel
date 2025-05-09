<?php

namespace App\Controller\Admin;

use App\Repository\TenantRepository;
use App\Service\Rcon\RconServiceInterface;
use App\Service\TenantServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WebConsoleController extends AbstractController
{
    #[Route(path: '/admin/webconsole', name: 'app_admin_webconsole_index', methods: ['GET'])]
    public function index(Request $request, TenantRepository $tenantRepository): Response
    {
        $tenant = $tenantRepository->find($request->get('tenant'));
        if (!$tenant) {
            throw $this->createNotFoundException('Tenant not found');
        }

        return $this->render('webconsole/index.html.twig', [
            'tenant' => $tenant,
        ]);
    }

    #[Route(path: '/admin/webconsole/sendcommand', name: 'app_admin_webconsole_send_command', methods: ['POST'])]
    public function sendCommand(Request $request, TenantServiceInterface $tenantService, RconServiceInterface $rconService): Response
    {
        $payload = $request->getPayload();
        $tenant = $tenantService->getTenant(
            (int) $payload->get('tenant'),
            false,
            true
        );
        if (!$tenant) {
            throw $this->createAccessDeniedException('Tenant not found for user.');
        }
        $command = $payload->get('command');
        $response = $rconService->sendCommand($command, $tenant);

        return $this->json([
            'raw' => $response,
            'response' => json_decode($response),
        ]);
    }
}
