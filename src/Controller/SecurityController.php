<?php

namespace App\Controller;

use DateTime;
use App\Entity\Users;
use Container1sHRer4\getSession_FactoryService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/inscription', name: 'app_register')]
    public function register(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passHasher): Response
    {
        //Cette méthode permet la création d'un compte Client via formulaire
        $date = new \DateTime('now');
        $entityManager = $doctrine->getManager();
        //Nous créons un formulaire interne pour l'inscription
        $userForm = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Email :',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre Email, il permettra de récupérer votre compte.',
                    'style' => 'margin-bottom: 1rem',
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Pseudo :',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre Pseudo, il permettra de vous idéentifier',
                    'style' => 'margin-bottom: 3rem',
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe :',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Renseigner votre Mot de passe',
                        'style' => 'margin-bottom: 1rem',
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Répéter votre Mot de Passe',
                        'style' => 'margin-bottom: 5rem;'
                    ]
                ]
            ])
            ->add('created_at', HiddenType::class, [
                'label' => 'Created_at :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre Pseudo',
                    'style' => 'margin-bottom: 1rem',
                    'value' => $date->format('d-m-Y H:i:s'),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'SOUMETTRE',
                'attr' => [
                    'class' => 'btn-yellow-large mt-5 position-absolute start-50 translate-middle',
                ]
            ])
            ->getForm();
        //On applique la Request sur notre formulaire
        $userForm->handleRequest($request);
        //On se prépare à utiliser le formulaire
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            //On récupère les informations de notre formulaire
            $data = $userForm->getData();
            $password = $data['password'];
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{9,}$/', $password)) {
                // Le mot de passe ne respecte pas le format requis
                // Gérer l'erreur ou afficher un message d'erreur approprié
                $this->addFlash(
                    'warning',
                    '🛑 Le mot de passe doit contenir au minimum 9 caractères, au moins 1 chiffre, 1 caractère spécial, 1 minuscule et 1 majuscule.'
                );
            } else {
                //Nous créons et renseignons notre Utilisateur
                $user = new Users;
                $user->setUsername($data['username']);

                $user->setRoles(['ROLE_USER', 'ROLE_MEMBER']);
                $user->setCreatedAt($data['created_at']);
                $user->setPassword($passHasher->hashPassword($user, $data['password']));
                //On persiste notre Entity
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    '✅ Utilisateur enregistré ; vous pouvez vous connecter.'
                );

                //Après le transfert de notre Entity User, on retourne sur l'index
                return $this->redirectToRoute('app_login');
            }
        }
        //Si notre formulaire n'est pas validé, nous le présentons à l'Utilisateur
        return $this->render('security/register.html.twig', [
            'formName' => 'Inscription',
            'dataForm' => $userForm->createView(),
        ]);
    }

    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session, Request $request): Response
    {
        if ($this->getUser()) {
            $this->addFlash(
                'success',
                '✅ Vous êtes connecté !'
            );
            return $this->redirectToRoute('app_index');
        }

        // get the login error if there is one
        // $error = $authenticationUtils->getLastAuthenticationError();
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error !== null) {
            $this->addFlash(
                'warning',
                '🛑 Les informations saisies sont incorrectes.'
            );
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'formName' => 'Connexion']);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Request $request)
    {
        return $this->redirectToRoute('app_deconnexion');
    }
}
