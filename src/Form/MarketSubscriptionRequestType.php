<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\MarketSubscriptionRequest;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarketSubscriptionRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => false,  'attr' => [
                    'class' => 'form-control col-6 bg-light',
                    'placeholder' => 'Enter your name'
               ] ])
            ->add('email', EmailType::class, [
                'label' => false,   'attr' => [
                    'class' => 'form-control col-6 bg-light',
                    'placeholder' => 'Enter your email'
                ]
            ])
            ->add('website', null, [
                'label' => false,  'attr' => [
                    'class' => 'form-control col-12 bg-light',
                    'placeholder' => 'Enter your website'
                ] ])
            ->add('address', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control col-12 bg-light',
                    'placeholder' => 'Enter your address'

                ] ])
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-select col-6 bg-light',
                    'placeholder' => 'Enter your city'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MarketSubscriptionRequest::class,
        ]);
    }
}
