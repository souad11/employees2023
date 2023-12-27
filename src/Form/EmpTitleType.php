<?php

namespace App\Form;

use App\Entity\EmpTitle;
use App\Entity\Employee;
use App\Entity\Title;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class EmpTitleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fromDate')
            //todate doit être a 01/01/9999
            ->add('toDate', DateType::class, [
                'widget' => 'single_text', //pour avoir un seul champ de saisie
                'format' => 'yyyy-MM-dd', //pour avoir le format attendu par MySQL
                'data' => new \DateTime('9999-01-01'), //pour avoir une date par défaut
            ])

            ->add('employee', EntityType::class, [
                'class' => Employee::class,
'choice_label' => function(Employee $employee) {
                return $employee->getFirstName() . ' ' . $employee->getLastName();
                },
            ])
            ->add('title', EntityType::class, [
                'class' => Title::class,
'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmpTitle::class,
        ]);
    }
}
