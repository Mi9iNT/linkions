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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class EmailController extends AbstractController
{

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

        $currentUser = $userRepository->find($user);

        $username = $currentUser->getUsername();
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
