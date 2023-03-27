<?php

namespace App\Form;

use App\Entity\SellerOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerOfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('creationDate')
            ->add('startDate')
            ->add('offer')
            ->add('seller');
    }

//add collection type
   /* public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('offer', HiddenType::class)
            ->add('seller', HiddenType::class)
            ->add('startDate', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'html5' => false,
            ]);
    }*/

//end of add
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SellerOffer::class,
        ]);
    }
}
