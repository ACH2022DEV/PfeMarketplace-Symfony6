<?php

namespace App\Form;

use App\Entity\Api;
use App\Entity\Seller;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('baseUrl')
            ->add('apiKeyValue')
            ->add('login')
            ->add('password', PasswordType::class)
//            ->add('seller', SellerType::class,[
//                'data_class' => Seller::class
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Api::class,
            'validation_groups' => ['Default', 'groupName'],
        ]);
    }
}
