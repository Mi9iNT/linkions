<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formation_title', TextType::class, [
                'label' => 'Titre de la formation :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner le titre de la formation',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('formation_centre_name', TextType::class, [
                'label' => 'Nom du centre de formation :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner le nom du centre de formation',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('formation_duree', TextType::class, [
                'label' => 'Durée de la formation :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner la durée de la formation',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('formation_date_debut', DateType::class, [
                'label' => 'Date du début de la formation :',
                'required' => false,
                'widget' => 'choice',
                'input'  => 'datetime_immutable',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => '',
                    'placeholder' => 'Renseigner la date du début de la formation au format JJ/MM/AAAA',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('formation_date_fin', DateType::class, [
                'label' => 'Date de fin de la formation :',
                'required' => false,
                'widget' => 'choice',
                'input'  => 'datetime_immutable',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => '',
                    'placeholder' => 'Renseigner la date de fin de la formation au format JJ/MM/AAAA',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add(
                'formation_details',
                TextareaType::class,
                [
                    'label' => 'Description',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Renseigner un résumer du dérouler de la formation',
                        'class' => 'form-control',
                        'style' => 'height:10rem; margin-bottom: 2rem;',
                    ]
                ]
            )
            ->add('formation_validee', ChoiceType::class, [
                'label' => 'Formation validée :',
                'required' => false,
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'placeholder' => 'Avez-vous validé cette formation ?',
                'empty_data' => null,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 2rem',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'SOUMETTRE',
                'attr' => [
                    'class' => 'btn-yellow-large mt-5 position-absolute start-50 translate-middle',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
