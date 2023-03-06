<?php

namespace App\Form;

use App\Entity\Offer;
use App\Form\OfferProdType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('nbProductTypes')
            ->add('nbDays')
            //the Code added in 3/03/2023
          ->add('offerProductTypes', CollectionType::class, array(
                'entry_type' => OfferProdType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ));

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
