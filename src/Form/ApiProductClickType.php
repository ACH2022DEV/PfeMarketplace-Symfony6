<?php

namespace App\Form;

use App\Entity\ApiProductClick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApiProductClickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('ipTraveler')
            ->add('ipLocation')
            ->add('traveler')
            ->add('apiProduct')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApiProductClick::class,
        ]);
    }
}
