<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Ajoutez ici les champs de votre formulaire de réinitialisation du mot de passe
            // Exemple :
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre nouveau mot de passe',
                    'style' => 'margin-bottom: 1rem',
                ]
            ])
            ->add('repeatPassword', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Confirmer le mot de passe',
                    'style' => 'margin-bottom: 5rem',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'SOUMETTRE',
                'attr' => [
                    'class' => 'btn-yellow-large mt-5 position-absolute start-50 translate-middle',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // $resolver->setDefaults([
        //     'data_class' => Users::class,
        // ]);
    }
}
