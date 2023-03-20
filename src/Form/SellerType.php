<?php

namespace App\Form;

use App\Entity\Api;
use App\Entity\City;
use App\Entity\Seller;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
                    'label'=>false
                ])
             /*   ->add('email', EmailType::class, [
                    'required' => true,
                    'data' => $options['data']->getUser()->getEmail(),
                    'label' => 'Email',
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'row_attr' => [
                        'class' => 'col-md-6',
                    ],
                ])*/
      /*   ->add('user', UserType::class,[
                'data_class' => User::class,
                //'required' => true,
                'label'=>false
            ])*/
            //->add('password')
             //    $builder->get('user')->remove('email')
            ->add('name', TextType::class, [
                 'label'=>false

             ])
            ->add('website', TextType::class, [
                'label'=>false

            ])
            ->add('address', TextType::class, [
                'label'=>false

            ])
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
                'label'=>false
    ])
//            ->add('api', EntityType::class, [
//                'required' => false,
//                'class'=> Api::class
//            ])
        ;

$builder->get('user')
           ->remove('password');
        $builder->get('user')
            ->remove('isVerified');
        $builder->get('user')
            ->remove('active');
        $builder->get('user')
            ->remove('display_name');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}
