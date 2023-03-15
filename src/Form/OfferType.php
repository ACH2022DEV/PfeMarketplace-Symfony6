<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\OfferProductType;
use App\Form\OfferProdType;
use Symfony\Component\Form\FormView;
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
            ->add('offerProductTypes', CollectionType::class, [
                'entry_type' => \App\Form\OfferProdType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name__',
                'label' => false,
                'by_reference' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
    public function addOfferProductType(OfferProductType $offerProductType): self
    {
        if (!$this->offerProductTypes->contains($offerProductType)) {
            //added in 3/03/2023
            /* $offerProductType->setOffer($this);
             $this->offerProductTypes[] = $offerProductType;*/
            $this->offerProductTypes->add($offerProductType);
            $offerProductType->setOffer($this);
        }

        return $this;
    }
}
