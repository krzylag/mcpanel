<?php

namespace App\Form;

use App\Service\TenantServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly TenantServiceInterface $tenantService,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tenantChoices = [];
        foreach ($this->tenantService->getCurrentDefinitions() as $tenantDto) {
            $tenantChoices[$tenantDto->getName()] = $this->translator->trans($tenantDto->getTranslationKey());
        }

        $builder
            ->add('tenant', ChoiceType::class, [
                'choices' => array_flip($tenantChoices),
                'required' => true,
                'expanded' => true,
            ])
            ->add('nick', TextType::class)
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
