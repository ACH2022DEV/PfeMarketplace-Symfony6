<?php

namespace App\Form;

use App\Entity\Api;
use App\Entity\City;
use App\Entity\Seller;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class,[
                'data_class' => User::class,
                //'required' => true,
                'label' => 'seller as user',
            ])
            ->remove('email')
            ->add('name')
            ->add('website')
            ->add('address')
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
    ])
            ->add('api', EntityType::class, [
                'required' => false,
                'class'=> Api::class
            ])
        ;

        $builder->get('user')
            ->remove('password');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}
