<?php

namespace App\Controller\Admin;

use App\Entity\Tenant;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlayerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere(sprintf(
                    "entity.roles LIKE '%%%s%%'",
                    User::ROLE_PLAYER,
                )
            );
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
        yield TextField::new('username');
        yield CollectionField::new('tenants')->formatValue(
            function (Collection $collection) {
                return implode(
                    ', ',
                    $collection->map(
                        function (Tenant $tenant) {
                            return $tenant->getName();
                        }
                    )->toArray()
                );
            }
        );
        yield DateTimeField::new('createdAt');
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
