<?php

namespace App\Controller\Admin;

use App\Entity\Tenant;
use App\Entity\User;
use App\Provider\TenantProvider;
use App\Repository\TenantRepository;
use App\Service\TenantService;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public function index(): Response
    {
        return $this->redirectToRoute('app_admin_dashboard');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Html');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addJsFile('https://code.jquery.com/jquery-3.7.1.min.js');
    }


    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('dashboard.menu.administrators', 'fas fa-solid fa-users', User::class)->setController(AdminCrudController::class);
        yield MenuItem::linkToCrud('dashboard.menu.players', 'fas fa-solid fa-gamepad', User::class)->setController(PlayerCrudController::class);
        yield MenuItem::linkToCrud('dashboard.menu.tenants', 'fas fa-solid fa-server', Tenant::class);
        yield MenuItem::subMenu('dashboard.menu.web_console', 'fa fa-solid fa-display')->setSubItems($this->configureTenantConsoleMenus());
    }

    private function configureTenantConsoleMenus(): array
    {
        $entries = [];
        $tenantProvider = $this->tenantService->getProvider();
        foreach ($tenantProvider->getArray() as $tenant) {
            $iconClass = 'fa-'.count($entries)+1;
            $entries[] = MenuItem::linkToRoute(
                $tenant->getTenantEntity()->getName(),
                'fas fa-solid ' . $iconClass,
                'app_admin_webconsole_index',
                [
                    'tenant' => $tenant->getTenantEntity()->getId()
                ]
            );
        }
        return $entries;
    }

    #[Route(path: '/admin/dashboard', name: 'app_admin_dashboard', methods: ['GET'])]
    public function sendCommand(TenantRepository $tenantRepository, TenantProvider $tenantProvider): Response
    {
        $tenantProvider->injectRepository($tenantRepository);
        return $this->render('dashboard/index.html.twig', [
            'tenants' => $tenantProvider->getArray(),
        ]);
    }
}
