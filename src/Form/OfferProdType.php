<?php

namespace App\Form;

use App\Entity\OfferProductType;
use App\Entity\Offer;

use App\Entity\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferProdType extends AbstractType
{
    private ProductType $productType;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('maxItems')
            ->add('price')
            ->add('offer', EntityType::class, [
                'required' => true,
                'class'=> Offer::class
            ])
            ->add('productType', EntityType::class, [
                'required' => true,
                'class'=> ProductType::class
            ])
        ;


        /* $builder
            ->add('maxItems')
            ->add('price')
            ->add('offer')
           //  $builder->add('ProductTypeName',ProductType::class)
            //->add('name')
        ->add('productType', EntityType::class, [
        'class' => ProductType::class,
        'choice_label' => 'name',
        'label' => 'Product Type',  ]);

       /* ->add('productType', EntityType::class, [
                'class' => ProductType::class,
                'choice_label' => 'name',
                'label' => 'Other Entity Name',
                // add more options as needed

          //  ->add('productType')
        ;*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OfferProductType::class
          //  'data_class2' => ProductType::class,
        ]);
    }
    public function setProductType(ProductType $productType): self
    {
        $this->productType = $productType;

        return $this;
    }
}
