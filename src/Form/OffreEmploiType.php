<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class OffreEmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom'
        ])
        ->add('prenom', TextType::class, [
            'label' => 'Prénom'
        ])
        ->add('date_de_naissance', DateType::class, [
            'label' => 'Date de naissance',
            'widget' => 'single_text',
        ])
        ->add('cv', FileType::class, [
            'label' => 'CV (PDF file)',
            'mapped' => false,
            'required' => true, 
            'attr' => [
                'accept' => '.pdf'
            ],
        ])

        ->add('Submit', SubmitType::class, [
            'attr' => array(
                'class' => 'btn btn-primary'
            )
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
