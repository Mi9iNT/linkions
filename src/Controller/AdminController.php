<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\VisibilityProfil;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    #Affiche tous les utilisateurs
    #[Route('/administration', name: 'app_admin')]
    public function adminIndex(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();

        if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('warning', '🛑 Vous n\'avez pas les habilitées nécessaires pour accéder à ce contenu.');
            return $this->redirectToRoute('app_index');
        }

        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $users = $userRepository->findBy([], ['id' => 'DESC']);

        return $this->render('admin/index.html.twig', [
            'title' => 'Administration',
            'user' => $user,
            'users' => $users,
        ]);
    }

    #[Route('/administration/visibility/{userId}/{userName}/{visibility}', name: 'app_admin_user_visibility')]
    public function updateAdminVisibility(ManagerRegistry $doctrine, Request $request, int $userId, string $visibility, string $userName): Response
    {
        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class)->find($userId);

        if (!$userRepository) {
            $request->getSession()->getFlashBag()->add('warning', '❌ Impossible de trouver le profil utilisateur demandé.');
            return $this->redirectToRoute('app_admin');
        }

        $visibilityProfil = $userRepository->getVisibilityProfil();
        if (!$visibilityProfil) {
            $visibilityProfil = new VisibilityProfil();
            $visibilityProfil->setUser($userRepository);
            $visibilityProfil->setAdminVisibility('visible');
        }


        try {
            // Mettre à jour la donnée 'admin_visibility' en fonction de la visibilité fournie
            if ($visibility === 'visible') {
                $visibilityProfil->setAdminVisibility('visible');
            } else {
                $visibilityProfil->setAdminVisibility('invisible');
            }
        } catch (FileException $e) {
            // Ajouter un message au flashbag
            $request->getSession()->getFlashBag()->add('warning', '❌ Une erreur est survenue lors de la modification de la visibilitée du profil utilisateur de ' . $userName . '.');
        }

        $entityManager->persist($visibilityProfil);
        $entityManager->flush();

        $request->getSession()->getFlashBag()->add('success', '✅ La visibilité du profil de l\'utilisateur "' . $userName . '" a été mise à jour avec succès.');

        return $this->redirectToRoute('app_admin');
    }


    #[Route('/administration/user/{userId}/{userName}', name: 'app_admin_add_admin_role')]
    public function addAdminRole(ManagerRegistry $doctrine, Request $request, int $userId, string $userName): Response
    {
        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class)->find($userId);

        if (!$userRepository) {
            $request->getSession()->getFlashBag()->add('warning', '🛑 Impossible de trouver le profil utilisateur demandé.');
            return $this->redirectToRoute('app_admin');
        }

        $userRoles = $userRepository->getRoles();
        try {
            if (!in_array('ROLE_ADMIN', $userRoles, true)) {
                $userRoles[] = 'ROLE_ADMIN';
                $userRepository->setRoles($userRoles);

                $entityManager->persist($userRepository);
                $entityManager->flush();

                $request->getSession()->getFlashBag()->add('success', '✅ Le rôle "Administrateur" a été donné à l\'utilisateur ' . $userName . '.');
            } else {
                $request->getSession()->getFlashBag()->add('warning', '🛑 ' . $userName . ' est déjà administrateur.');
            }
        } catch (FileException $e) {
            // Ajouter un message au flashbag
            $request->getSession()->getFlashBag()->add('warning', '❌ Une erreur est survenue lors de l\'ajout du rôle "Administrateur" à l\'utilisateur ' . $userName . '.');
        }


        return $this->redirectToRoute('app_admin');
    }
}
