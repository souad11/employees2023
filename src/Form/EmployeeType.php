<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Gender;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;



class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $isUserEdit = $options['is_user_edit'];
        
        $builder


            ->add('birthDate', DateType::class, [
                'disabled' => $isUserEdit,
            ])


            ->add('firstName', TextType::class, [
                'disabled' => $isUserEdit,
            ])
            

            ->add('lastName', TextType::class, [
                'disabled' => $isUserEdit,
            ])

            ->add('gender', EnumType::class, [
                'class' => Gender::class,
                'disabled' => $isUserEdit,
            ])
            ->add('hireDate', DateType::class, [
                'disabled' => $isUserEdit,
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo (JPG file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k', //1Mo
                        'mimeTypes' => [
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG document',
                    ])
                ],
            ])
            ->add('email', EmailType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
            'is_user_edit' => false,
        ]);
    }
}
