<?php

namespace App\Form;

use App\Entity\Api;
use App\Entity\City;
use App\Entity\Seller;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
                //add a file
                ->add('brochure', FileType::class, [
                    'label' => false,

                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,

                    // make it optional so you don't have to re-upload the PDF file
                    // every time you edit the Product details
                    'required' => false,

                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                                'image/png',
                                'image/jpeg',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid PDF document',
                        ])
                    ],
                    /*'help' => ' alt="Camera icon"> Click here to upload a PDF document or image',
                    'attr' => [
                        'class' => 'custom-file-input',
                        'accept' => 'application/pdf,image/jpeg,image/png',
                        'onchange' => 'readURL(this);',
                    ],*/
                ])
                    //end of add file
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
