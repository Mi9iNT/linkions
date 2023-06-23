<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Consent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConsentementController extends AbstractController
{
    #[Route('/consentement/{consent}', name: 'app_cookie_consent')]
    public function cookiesConsent(Request $request, ManagerRegistry $doctrine, string $consent): Response
    {
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté et si un fichier de cookie existe déjà
        if ($user && !$request->cookies->has('cookie_consent')) {
            // Si l'utilisateur est connecté mais n'a pas de fichier de cookie, utiliser la valeur enregistrée en base de données
            $consent = $user->getConsent();
            $response = new Response();
            $response->headers->setCookie(new Cookie('cookie_consent', $consent));
            $response->send();
        } elseif (!$user) {
            // Si l'utilisateur n'est pas connecté, enregistrer le consentement dans un cookie de session
            if ($consent === 'true' || $consent === 'false') {
                $response = new Response();
                $response->headers->setCookie(new Cookie('cookie_consent', $consent));
                $response->send();

                $this->addFlash('success', '✅ Consentement pris en compte.');
            } else {
                $this->addFlash('warning', '❌ Consentement non pris en compte.');
            }
        } else {
            // Si l'utilisateur est connecté, enregistrer le consentement en base de données et mettre à jour le champ "consent"
            if ($consent === 'true' || $consent === 'false') {
                $entityManager = $doctrine->getManager();
                $user->setConsent($consent);

                $consentEntity = new Consent();
                $consentEntity->setConsentValue($consent);
                $consentEntity->setUserConsent($user);

                $entityManager->persist($consentEntity);
                $entityManager->flush();

                $this->addFlash('success', '✅ Consentement pris en compte.');
            } else {
                $this->addFlash('warning', '❌ Consentement non pris en compte.');
            }
        }

        return $this->redirectToRoute('app_index');
    }
}
