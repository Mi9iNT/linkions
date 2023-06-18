<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuperAdminController extends AbstractController
{
    #[Route('/super/admin', name: 'app_super_admin')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user || !in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $request->getSession()->getFlashBag()->add('warning', '🛑 Vous n\'avez pas les habilitées nécessaires pour accéder à ce contenu.');
            return $this->redirectToRoute('app_index');
        }
        return $this->render('super_admin/index.html.twig', [
            'controller_name' => 'SuperAdminController',
        ]);
    }


    #[Route('/administration/{userId}/{userName}/supprimer/role/admin', name: 'app_super_admin_remove_role_admin')]
    public function removeAdminRole(ManagerRegistry $doctrine, Request $request, int $userId, string $userName): Response
    {
        $user = $this->getUser();

        if (!$user || !in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $request->getSession()->getFlashBag()->add('warning', '🛑 Vous n\'avez pas les habilités nécessaires pour accéder à ce contenu.');
            return $this->redirectToRoute('app_index');
        }

        // Récupérer l'ID de l'utilisateur à partir de la requête
        $userId = $request->get('userId');

        // Récupérer l'EntityManager
        $entityManager = $doctrine->getManager();

        // Récupérer l'utilisateur à partir de l'ID
        $userToRemoveRole = $entityManager->getRepository(Users::class)->find($userId);

        if (!$userToRemoveRole) {
            // Gérer le cas où l'utilisateur n'est pas trouvé
            $request->getSession()->getFlashBag()->add('warning', '❌ l\'utilisateur ' . $userName . ' n\'a pas été trouvé.');
            return $this->redirectToRoute('app_admin');
        } else {
            // Récupérer les rôles actuels de l'utilisateur
            $roles = $userToRemoveRole->getRoles();

            // Vérifier si le rôle "ROLE_ADMIN" est présent
            $key = array_search('ROLE_ADMIN', $roles);
            if ($key !== false) {
                // Supprimer le rôle "ROLE_ADMIN" du tableau des rôles
                unset($roles[$key]);

                // Mettre à jour les rôles de l'utilisateur
                $userToRemoveRole->setRoles($roles);

                // Enregistrer les modifications dans la base de données
                $entityManager->flush();

                // Gérer le cas où le rôle a été supprimé avec succès
                $request->getSession()->getFlashBag()->add('success', '✅ Le rôle "Administrateur" de l\'utilisateur ' . $userName . ' à été supprimé avec succès.');
                return $this->redirectToRoute('app_admin');
            } else {
                // Gérer le cas où le rôle "ROLE_ADMIN" n'est pas trouvé
                $request->getSession()->getFlashBag()->add('warning', '🛑 ' . $userName . ' n\'est pas "Administrateur.');
                return $this->redirectToRoute('app_admin');
            }
        }

        return $this->redirectToRoute('app_admin');
    }
}
