<?php

namespace App\Form;

use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('poste_name', TextType::class, [
                'label' => 'Nom du poste :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner le nom du poste occupé',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('tache_realisee', TextareaType::class, [
                'label' => 'Tâche(s) réalisée(s) :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner la ou les tâches que vous avez réalisé.',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('duree_contrat', DateIntervalType::class, [
                'label' => 'Durée du contrat :',
                'required' => false,
                'widget' => 'integer',
                // 'input' => 'string',
                'with_years' => true,
                'with_months' => true,
                'with_days' => false,
                'with_hours' => false,
                'with_minutes' => false,
                'with_seconds' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner la durée du contrat',
                    'style' => 'margin-bottom: 2rem',
                ],
                'labels' => [
                    'years' => 'Mois',
                    'months' => 'Années',
                    'days' => 'Jours',
                    'hours' => 'Heures',
                    'minutes' => 'Minutes',
                    'seconds' => 'Secondes',
                ],
            ])
            ->add('entreprise_name', TextType::class, [
                'label' => 'Nom de l\'entreprise :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner le nom de l\'entreprise',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('entreprise_location', TextType::class, [
                'label' => 'Localisation de l\'entreprise :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner la localisation de l\'entreprise',
                    'style' => 'margin-bottom: 2rem',
                ]
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
            'data_class' => Experience::class,
        ]);
    }
}
