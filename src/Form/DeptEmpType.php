<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\DeptEmp;
use App\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeptEmpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'deptName',
                'placeholder' => 'Choose a department',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DeptEmp::class,
        ]);
    }
}
