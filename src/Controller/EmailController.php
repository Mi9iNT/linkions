<?php

namespace App\Controller;

use App\Entity\Users;
use App\Service\MailerService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ResetPasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class EmailController extends AbstractController
{

    /**
     * Récupérer compte
     */

    # Récupération compte, permet si l'adresse email fournies est trouver en bdd et si l'username correspond à l'username de l'email renseigner pour lui envoyer un mail à l'adresse mail avec un lien permettant de modifier son mot de passe
    #[Route('/recuperation-de-compte', name: 'app_recup_account')]
    public function recupAccount(Request $request, ManagerRegistry $doctrine, MailerService $mailerService): Response
    {
        // Vérifier si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            // Rediriger l'utilisateur vers une page appropriée
            return $this->redirectToRoute('app_index');
        }

        #Formulaire de récupération de compte
        $sendEmailForm = $this->createFormBuilder()
            ->add('to', EmailType::class, [
                'label' => 'Email :',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre email',
                ]
            ])
            ->add('from', HiddenType::class, [
                'data' => 'linkions.contact@linkions.fr',
            ])
            ->add('subject', HiddenType::class, [
                'data' => 'Récupération de compte'
            ])
            ->add(
                'username',
                textType::class,
                [
                    'label' => 'pseudo',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Renseigner votre pseudo',
                        'class' => 'form-control mb-5',
                    ]
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => 'ENVOYER',
                'attr' => [
                    'class' => 'btn-yellow-large mt-5 position-absolute start-50 translate-middle my-5',
                ]
            ])
            ->getForm();


        $sendEmailForm->handleRequest($request);

        if ($sendEmailForm->isSubmitted() && $sendEmailForm->isValid()) {
            $formData = $sendEmailForm->getData();
            // Vérifier si l'email et le nom d'utilisateur correspondent en base de données
            $entityManager = $doctrine->getManager();
            $userRepository = $entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy([
                'email' => $formData['to'],
                'username' => $formData['username'],
            ]);
            if ($user) {
                // Générer un token unique pour la réinitialisation du mot de passe
                $token = uniqid();

                // Enregistrer le token dans la base de données pour l'utilisateur
                $user->setResetMdp($token);
                $entityManager->flush();

                $username = $formData['username'];

                // Envoyer l'e-mail de réinitialisation avec le token
                $resetLink = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $mailerService->sendEmail(
                    $formData['to'],
                    $formData['from'],
                    $formData['subject'],
                    $formData['username'],
                    $username,
                    $resetLink,
                    'email/recupAccountTemplate.html.twig',


                );

                $this->addFlash('success', '✅ Email envoyé !');
                return $this->redirectToRoute('app_index');
            } else {
                // Le compte n'a pas été trouvé, afficher un message d'erreur
                $this->addFlash('error', '❌ Compte non trouvé. Veuillez contacter un administrateur.');
            }
        }

        return $this->render('email/email.html.twig', [
            'title' => 'Récupération de compte',
            'formName' => 'Récupération de compte',
            'sendEmailForm' => $sendEmailForm->createView(),
            'year' => new \DateTime('now'),
        ]);
    }

    /**
     * Réinitialiser mot de passe si mot de passe oublier
     */

    # Réinitialiser mot de passe {token} permet de réinitialiser le mot de passe d'un utilisateur en fonction des informations passées dans le formulaire de récupération de compte et le token obtenu
    #[Route('/reinitialiser-mot-de-passe/{token}', name: 'app_reset_password')]
    public function resetPassword(Request $request, ManagerRegistry $doctrine, string $token, UserPasswordHasherInterface $passHasher): Response
    {
        // Récupérer l'utilisateur correspondant au token de réinitialisation
        $user = $doctrine->getRepository(Users::class)->findOneBy(['resetMdp' => $token]);

        if (!$user) {
            // Afficher un message d'erreur si le token n'est pas valide
            $this->addFlash('warning', '❌ Identifiant de réinitialisation invalide.');
            return $this->redirectToRoute('app_index');
        }

        // Créer le formulaire de réinitialisation du mot de passe (à implémenter)
        $resetForm = $this->createForm(ResetPasswordType::class);

        $resetForm->handleRequest($request);
        $password = $resetForm['password']->getData();
        $repeatPassword = $resetForm['repeatPassword']->getData();

        if ($resetForm->isSubmitted() && $resetForm->isValid()) {

            if ($password == $repeatPassword && preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{9,}$/', $password)) {
                // Le mot de passe respecte le format requis
                // Réinitialiser le mot de passe de l'utilisateur et enregistrer les modifications
                $user->setPassword($passHasher->hashPassword($user, $password));
                $user->setResetMdp(null);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // Rediriger l'utilisateur vers une page appropriée après la réinitialisation du mot de passe
                $this->addFlash('success', '✅ Mot de passe réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            } else {
                // Le mot de passe ne respecte pas le format requis
                // Gérer l'erreur ou afficher un message d'erreur approprié
                $this->addFlash(
                    'warning',
                    '🛑 Le mot de passe doit contenir au minimum 9 caractères, au moins 1 chiffre, 1 caractère spécial, 1 minuscule et 1 majuscule.'
                );
            }
        }

        return $this->render('security/resetMdp.html.twig', [
            'title' => 'Réinitialisation du Mot de passe',
            'resetForm' => $resetForm->createView(),
            'formName' => 'Réinitialisation du Mot de passe'
        ]);
    }




    /**
     * Nous contacter
     */

    # NousContacter, permet d'envoyer un e-mail à l'adresse de contact de l'application
    #[Route('/nous-contacter', name: 'app_contact_us')]
    public function contactUs(Request $request, ManagerRegistry $doctrine, MailerService $mailerService): Response
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);

        if ($user) {
            $currentUser = $userRepository->find($user);
            $username = $currentUser->getUsername();
        }

        ####### Formulaire de Contact #######
        $sendEmailForm = $this->createFormBuilder()
            ->add('to', EmailType::class, [
                'label' => 'Email :',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre email de contact pour avoir une réponse',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('from', HiddenType::class, [
                'data' => 'linkions.contact@linkions.fr',
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet :',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner le sujet de votre demande',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Demande',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Renseigner votre demande',
                    'class' => 'form-control',
                    'style' => 'height:10rem;',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'ENVOYER',
                'attr' => [
                    'class' => 'btn-yellow-large mt-5 position-absolute start-50 translate-middle my-5',
                ]
            ])
            ->getForm();

        $sendEmailForm->handleRequest($request);

        if ($sendEmailForm->isSubmitted() && $sendEmailForm->isValid()) {
            $messageData = $sendEmailForm->getData();

            // Envoyer l'e-mail en utilisant le service MailerInterface
            $mailerService->sendEmail(
                $messageData['from'],
                $messageData['to'],
                $messageData['subject'],
                $messageData['message'],
                '',
                'Email de Contact',
                'email/contactUsTemplate.html.twig'
            );


            // Faire quelque chose après l'envoi du message
            $this->addFlash('success', '✅ Email envoyé !');
            return $this->redirectToRoute('app_index');
        }


        return $this->render('email/email.html.twig', [
            'title' => 'Nous contacter',
            'formName' => 'Nous contacter',
            'sendEmailForm' => $sendEmailForm->createView(),
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }


    /**
     * Envoyer email à userId
     */

    #Utilisateur/contact/, permet d'envoyer un email si l'utilisateur à renseigner son email'
    #[Route('/utilisateur/contact/{username}/{userId}', name: 'app_utilisateur_send_mail')]
    public function utilisateurSendMail(string $username, int $userId, Request $request, ManagerRegistry $doctrine, MailerService $mailerService): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', '🛑 Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }

        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);

        $currentUser = $userRepository->find($user);

        $currentUsername = $currentUser->getUsername();
        $currentUserMail = $currentUser->getEmail();

        $membre = $userRepository->find($userId);
        if (!$membre) {
            $this->addFlash('danger', '❌ Utilisateur introuvable.');
            return $this->redirectToRoute('app_membre');
        }

        $membreEmail = $membre->getEmail();
        $membreId = $membre->getId();
        $membreUsername = $membre->getUsername();

        ####### Formulaire Utilisateur #######
        $sendEmailForm = $this->createFormBuilder($membre)
            ->add('subject', TextType::class, [
                'label' => 'Sujet :',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner le sujet de votre message',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Renseigner votre Message',
                    'class' => 'form-control',
                    'style' => 'height:10rem;',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'ENVOYER',
                'attr' => [
                    'class' => 'btn-yellow-large mt-5 position-absolute start-50 translate-middle my-5',
                ]
            ])
            ->getForm();

        $sendEmailForm->handleRequest($request);

        if ($sendEmailForm->isSubmitted() && $sendEmailForm->isValid()) {
            $messageData = $sendEmailForm->getData();

            // Envoyer l'e-mail en utilisant le service MailerInterface
            $mailerService->sendEmail(
                $membreEmail,
                $currentUserMail,
                $sendEmailForm->get('subject')->getData(),
                $sendEmailForm->get('message')->getData(),
                $membreUsername,
                '',
                'email/emailTemplate.html.twig'
            );

            // Faire quelque chose après l'envoi du message
            $this->addFlash('success', '✅ Email envoyé !');
            return $this->redirectToRoute('app_membre_infos', ['userId' => $membreId, 'userName' => $membreUsername]);
        }

        return $this->render('email/email.html.twig', [
            'title' => 'Contacter ' . $membreUsername,
            'formName' => 'Envoie d\'Email à ',
            'sendEmailForm' => $sendEmailForm->createView(),
            'user' => $user,
            'username' => $membreUsername,
            'year' => new \DateTime('now'),
        ]);
    }

    /**
     * Envoyer email aux administrateurs
     */

    # Utilisateur/message, permet d'envoyer un e-mail aux administrateurs de l'application
    #[Route('/utilisateur/contact/administrateur', name: 'app_utilisateur_send_admin_mail')]
    public function utilisateurSendAdminMail(Request $request, ManagerRegistry $doctrine, MailerService $mailerService): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', '🛑 Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }

        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);

        $currentUser = $userRepository->find($user);

        $username = $currentUser->getUsername();
        $currentUserMail = $currentUser->getEmail();

        $conn = $entityManager->getConnection();
        $sql = 'SELECT * FROM users WHERE JSON_CONTAINS(roles, :role) = 1';
        $membres = $conn->executeQuery($sql, ['role' => '"ROLE_ADMIN"'])->fetchAllAssociative();

        if (!$membres) {
            $this->addFlash('danger', '❌ Aucun administrateur trouvé.');
            return $this->redirectToRoute('app_utilisateur');
        }

        $sendEmailForm = $this->createFormBuilder()
            ->add('subject', TextType::class, [
                'label' => 'Sujet :',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner le sujet de votre message',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Renseigner votre Message',
                    'class' => 'form-control',
                    'style' => 'height:10rem;',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'ENVOYER',
                'attr' => [
                    'class' => 'btn-yellow-large mt-5 position-absolute start-50 translate-middle my-5',
                ]
            ])
            ->getForm();

        $sendEmailForm->handleRequest($request);

        if ($sendEmailForm->isSubmitted() && $sendEmailForm->isValid()) {
            $messageData = $sendEmailForm->getData();

            foreach ($membres as $membre) {
                // Envoyer l'e-mail en utilisant le service MailerInterface
                $mailerService->sendEmail(
                    $membre['email'],
                    $currentUserMail,
                    $sendEmailForm->get('subject')->getData(),
                    $sendEmailForm->get('message')->getData(),
                    $username,
                    '',
                    'email/emailAdminTemplate.html.twig'
                );
            }



            $this->addFlash('success', '✅ E-mail envoyé !');
            return $this->redirectToRoute('app_index');
        }

        return $this->render('email/email.html.twig', [
            'title' => 'Contacter Administrateur',
            'formName' => 'Envoi d\'e-mail aux administrateurs',
            'sendEmailForm' => $sendEmailForm->createView(),
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }
}
