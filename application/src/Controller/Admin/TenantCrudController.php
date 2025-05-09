<?php

namespace App\Controller\Admin;

use App\Entity\Tenant;
use App\Service\TenantService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TenantCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Tenant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if ($pageName === Crud::PAGE_INDEX) {
            return $this->configureFieldsOnIndex();
        } else {
            return $this->configureFieldsOnForm();
        }
    }

    public function configureFieldsOnIndex(): iterable
    {
        yield IdField::new('id');
        yield TextField::new('name');
        yield TextField::new('mcTenantId');
        yield TextField::new('registrationPassword');
    }

    public function configureFieldsOnForm(): iterable
    {
        yield TextField::new('name');
        yield ChoiceField::new('mcTenantId')
            ->setChoices($this->getTenantHostChoices()
        );
        yield TextField::new('registrationPassword');
    }

    private function getTenantHostChoices(): array
    {
        $result = [];
        foreach ($this->tenantService->getTenants() as $tenant) {
            $result[$tenant->getMcTenantId()] = $tenant->getMcTenantId();
        }
        return $result;
    }
}
