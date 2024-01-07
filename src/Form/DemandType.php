<?php

namespace App\Form;

use App\Entity\Demand;
use App\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Repository\EmployeeRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DemandType extends AbstractType
{

    

    public function buildForm(FormBuilderInterface $builder, array $options): void
    
    

    {

        $user = $options['user'];

        $roles= $user->getRoles();

        var_dump($roles);

        
        if ($options['is_edit_mode'] == false) {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Salary' => 'Salary',
                    'Vacation' => 'Vacation',
                    'Reassignment' => 'Reassignment',
                ],
            ])
            ->add('about')
            ->add('status', TextType::class, [
                'disabled' => true,
                'data' => null,
            ])
            ->add('employe', EntityType::class, [

                'class' => Employee::class,
                'query_builder' => function (EmployeeRepository $er) use ($user) {
                    return $er->createQueryBuilder('e')
                        ->where('e.id = :user') 
                        ->setParameter('user', $user)
                    ;
                },
            ]);

            
        } else if ($options['is_edit_mode'] == true && in_array('ROLE_ADMIN', $roles)) {
                $builder->add('status', ChoiceType::class, [
                    'choices' => [
                        'Pending' => null,
                        'Accepted' => true,
                        'Rejected' => false,
                    ],
                ])
                ->add('employe', EntityType::class, [
                    'class' => Employee::class,
                    'choice_label' => function (Employee $employee) {
                        return $employee->getFirstName() . ' ' . $employee->getLastName();
                    },
                ])
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'Salary' => 'Salary',
                        'Vacation' => 'Vacation',
                        'Reassignment' => 'Reassignment',
                    ],
                ])
                ->add('about');
                
        } else {
            $builder 

            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Salary' => 'Salary',
                    'Vacation' => 'Vacation',
                    'Reassignment' => 'Reassignment',
                ],
            ])

            ->add('about')
            
            ->add('status', TextType::class, [
                'disabled' => true,
                'data' => null,
            ])

            ->add('employe', EntityType::class, [

                'class' => Employee::class,
                'query_builder' => function (EmployeeRepository $er) use ($user) {
                    return $er->createQueryBuilder('e')
                        ->where('e.id = :user')
                        ->setParameter('user', $user)
                    ;
                },
            ]);


        }

    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demand::class,
            'user' => null,
            'is_edit_mode' => false,
        ]);
    }
    
}

