<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Avatar;
use App\Entity\Skills;
use App\Entity\Formation;
use App\Entity\Experience;
use App\Form\FormationType;
use App\Form\ExperienceType;
use App\Entity\CurriculumVitae;
use App\Entity\VisibilityProfil;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class IndexController extends AbstractController
{

    /**
     * Acceuil
     */

    #/, renvoie à l'acceuil en affichant les 10 derniers membres inscrit
    #[Route('/Acceuil', name: 'app_index')]
    public function index(ManagerRegistry $doctrine, Request $request, SessionInterface $session): Response
    {

        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $users = $userRepository->findBy([], ['id' => 'DESC']);


        array_splice($users, 10);

        // $messages = $session->getFlashBag()->get('success');
        // $request->getSession()->getFlashBag()->add('success', 'Fuck it !');


        return $this->render('index/index.html.twig', [
            'title' => 'Accueil',
            'year' => (new \DateTime('now')),
            'user' => $user,
            'users' => $users,
        ]);
    }

    /**
     * Deconnexion
     */

    #/, renvoie à l'acceuil en affichant les 5 derniers membres inscrit
    #[Route('/deconnexion', name: 'app_deconnexion')]
    public function deconnexion(ManagerRegistry $doctrine, Request $request): Response
    {

        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $users = $userRepository->findBy([], ['id' => 'DESC']);


        array_splice($users, 5);

        $request->getSession()->getFlashBag()->add('success', '✅ Vous êtes déconnecté !');


        return $this->render('index/index.html.twig', [
            'title' => 'Accueil',
            'year' => (new \DateTime('now')),
            'user' => $user,
            'users' => $users,
        ]);
    }

    /**
     * Changer visibilité profil utilisateur
     */

    #VisibilityUser permet de renseigner l'information qui permettra d'activer ou désactiver la visibilité du profil
    #[Route('/utilisateur/visibilite', name: 'app_visibility_profil')]
    public function visibilityUser(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $visibilityProfilRepository = $entityManager->getRepository(VisibilityProfil::class);
        $visibility = $request->request->get('visibility');
        $user = $this->getUser();
        if (!$user) {
            $request->getSession()->getFlashBag()->add('warning', '🛑 Veuillez vous connecter pour accéder à ce contenu.');
            return $this->redirectToRoute('app_login');
        }

        $visibilityProfil = $user->getVisibilityProfil();
        if (!$visibilityProfil) {
            $visibilityProfil = new VisibilityProfil();
            $user->setVisibilityProfil($visibilityProfil);
        }

        $adminVisibility = $visibilityProfil->getAdminVisibility();
        if (!$adminVisibility || $adminVisibility === 'true') {
            if ($visibility === 'visible') {
                $visibilityProfil->setUserVisbility('visible');
            } else {
                $visibilityProfil->setUserVisbility('invisible');
            }
        } else {
            $request->getSession()->getFlashBag()->add('warning', '🛑 Vous ne pouvez pas modifier cette donnée car votre profil a été masqué par un administrateur.');
            return $this->redirectToRoute('app_utilisateur', ['result' => 'error']);
        }

        $entityManager->persist($visibilityProfil);
        $entityManager->flush();

        $request->getSession()->getFlashBag()->add('success', '✅ Visibilité du profil changée avec succès.');
        return $this->redirectToRoute('app_utilisateur', ['result' => 'success']);
    }




    /**
     * Infos utilisateur connecté
     */

    #Utilisateur, affiche les données de l'utilisateur en cours
    #[Route('/utilisateur', name: 'app_utilisateur')]
    public function utilisateur(ManagerRegistry $doctrine, SessionInterface $session, Request $request): Response
    {
        $users = $this->getUser();

        if (!$users) {
            $request->getSession()->getFlashBag()->add('warning', '🛑 Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }

        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $user = $userRepository->findOneBy(['id' => $users]);

        if (!$user) {
            $request->getSession()->getFlashBag()->add('danger', '❌ Utilisateur introuvable.');
            return $this->redirectToRoute('app_index'); // Rediriger vers une autre page
        }

        return $this->render('index/utilisateur.html.twig', [
            'title' => 'Profil Utilisateur',
            'user' => $user,
            'year' => (new \DateTime('now')),
        ]);
    }


    /**
     * Modifier infos utilisateur connecté
     */

    #Utilisateur/modifier, permet de renseigner ou modifier les informations enregistrer en base de donnée
    #[Route('/utilisateur/modifier/informations/{userId}', name: 'app_utilisateur_infos')]
    public function utilisateurAddInfos(int $userId, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $users = $this->getUser();

        if (!$users) {
            $request->getSession()->getFlashBag()->add('danger', '🛑 Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
        }

        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $avatarRepository = $entityManager->getRepository(Avatar::class);
        $cvRepository = $entityManager->getRepository(CurriculumVitae::class);

        $user = $userRepository->find($users);
        if (!$user) {
            $request->getSession()->getFlashBag()->add('danger', '❌ Utilisateur introuvable.');
            return $this->redirectToRoute('app_utilisateur');
        }
        ####### Formulaire Utilisateur #######
        $userForm = $this->createFormBuilder($user)
            ->add('job_title', TextType::class, [
                'label' => 'Métier :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner l\'intitulé de votre Poste/Métier',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Avatar :',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 2rem',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Veuillez sélectionner un fichier d\'image valide (jpg, jpeg, png, svg, gif / taille max = 1024ko)',
                    ])
                ],
            ])

            ->add('curriculumVitae', FileType::class, [
                'label' => 'Currivulum Vitae :',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 2rem',
                ],

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuiller sélectionner un fichier de type pdf et de 1024ko max',
                    ])
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre Nom',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre Prénom',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre Email',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('phone_number', NumberType::class, [
                'label' => 'Téléphone :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre Numéro de téléphone',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Localisation :',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Renseigner votre Localisation',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('birthday', BirthdayType::class, [
                'label' => 'Date de naissance :',
                'required' => false,
                'format' => 'dd-MM-yyyy',
                'placeholder' => [
                    'day' => 'Jour', 'month' => 'Mois', 'year' => 'Année',
                ],
                'attr' => [
                    'class' => '',
                    'style' => 'margin-bottom: 2rem',
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'required' => true,
                'choices' => [
                    'Freelance' => 'freelance',
                    'Salarié' => 'salarié',
                    'En recherche' => 'en_recherche',
                ],
                'attr' => [
                    'class' => 'form-check-inline my-4 d-flex justify-content-between',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Renseigner votre Description',
                        'class' => 'form-control',
                        'style' => 'height:10rem;',
                    ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'SOUMETTRE',
                    'attr' => [
                        'class' => 'btn-yellow-large mt-5 position-absolute start-50 translate-middle my-5',
                    ]
                ]
            )
            ->getForm();

        $userForm->handleRequest($request);


        if ($userForm->isSubmitted()) {
            /** @var UploadedFile $avatar */
            /** @var UploadedFile $cv */
            $avatar = $userForm->get('avatar')->getData();
            $cv = $userForm->get('curriculumVitae')->getData();

            if ($avatar) {
                $avatarFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeAvatar = $slugger->slug($avatarFilename);
                $newAvatarFilename = $safeAvatar . '-' . uniqid() . '.' . $avatar->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $avatar->move(
                        $this->getParameter('avatar_directory'),
                        $newAvatarFilename
                    );
                } catch (FileException $e) {
                    // Ajouter un message au flashbag
                    $request->getSession()->getFlashBag()->add('warning', '❌ Une erreur est survenue lors de l\'ajout de l\'avatar');
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $avatarRepository = new Avatar();
                $avatarRepository->setAvatarName($newAvatarFilename);
                $user->setAvatar($avatar);
            }
            if ($cv) {
                $cvFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeCv = $slugger->slug($cvFilename);
                $newCvFilename = $safeCv . '-' . uniqid() . '.' . $cv->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cv->move(
                        $this->getParameter('cv_directory'),
                        $newCvFilename
                    );
                } catch (FileException $e) {
                    // Ajouter un message au flashbag
                    $request->getSession()->getFlashBag()->add('warning', '❌ Une erreur est survenue lors de l\'ajout du CV');
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $cvRepository = $user->getCurriculumVitae() ?? new CurriculumVitae;
                $cvRepository->setCurriculumName($newCvFilename);
                $user->setCurriculumVitae($cvRepository);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            // Ajouter un message au flashbag
            $request->getSession()->getFlashBag()->add('success', '✅ Information(s) enregistrée(s) !');
            return $this->redirectToRoute('app_utilisateur');
        }

        return $this->render('index/dataForm.html.twig', [
            'title' => 'Profil Utilisateur',
            'formName' => 'Fomulaire infos de ',
            'userForm' => $userForm->createView(),
            'user' => $users,
            'userId' => $userId,
            'year' => (new \DateTime('now')),
        ]);
    }

    /**
     * Ajout compétence utilisateur connecté
     */

    #Utilisateur/ajout/competence, permet de renseigner les compétences à enregistrer en base de données
    #[Route('/utilisateur/ajout/informations/{userId}/competences', name: 'app_utilisateur_competences_infos')]
    public function utilisateurAddCompetences(int $userId, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $skillsRepository = $entityManager->getRepository(Skills::class);
        $userRepository = $entityManager->getRepository(Users::class);

        $skillsForm = $this->createFormBuilder()
            ->add('users', HiddenType::class, [
                'attr' => [
                    'value' => intval($userId),
                ]
            ])
            ->add('tags', TextType::class, [
                'label' => 'Compétences :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Renseigner vos compétences en les séparant avec un ";"',
                    'class' => 'w3-input w3-border w3-round w3-light-grey input-competences',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
                'attr' => [
                    'class' => 'btn btn-form-sub',
                    'style' => 'margin-top:10px',
                ]
            ])
            ->getForm();

        $skillsForm->handleRequest($request);

        if ($skillsForm->isSubmitted() && $skillsForm->isValid()) {
            try {
                $data = $skillsForm->getData();
                $tags = explode(';', $data['tags']); // Séparer les tags par le caractère ';'

                $existingSkills = new ArrayCollection();

                foreach ($tags as $tag) {
                    $tagName = trim($tag); // Supprimer les espaces avant et après le tag

                    if (!empty($tagName)) {
                        $existingTag = $skillsRepository->findOneBy(['name' => $tagName]);

                        if ($existingTag) {
                            // Vérifier si l'utilisateur n'est pas déjà associé à cette compétence
                            $users = $existingTag->getUsers();

                            if (!$users->contains($user)) {
                                $users->add($user);
                                $existingSkills->add($existingTag);
                            }
                        } else {
                            $skill = new Skills;
                            $skill->setName($tagName);
                            $skill->addUser($user);
                            $entityManager->persist($skill);
                            $existingSkills->add($skill);
                        }
                    }
                }

                $entityManager->flush();

                $request->getSession()->getFlashBag()->add('success', '✅ Compétence(s) ajoutée(s).');

                return $this->redirectToRoute('app_utilisateur');
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('danger', ' ❌ Une erreur est survenue lors de l\'ajout d\'une ou plusieurs compétences.');
            }
        }

        return $this->render('index/dataForm.html.twig', [
            'title' => 'Profil Utilisateur',
            'skillsFormName' => 'Formulaire d\'ajout de Compétences',
            'skillsForm' => $skillsForm->createView(),
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }

    /**
     * Supprimer compétence utilisateur connecté
     */

    #Utilisateur/supprimer, permet de supprimer les compétences enregistrées en base de données
    #[Route('/utilisateur/supprimer/competence/{competenceId}/{userId}', name: 'app_utilisateur_remove_competence')]
    public function utilisateurRemoveCompetence(int $competenceId, int $userId, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $skillsRepository = $entityManager->getRepository(Skills::class);

        $competence = $skillsRepository->find($competenceId);

        if ($competence && $competence->getUsers()->contains($user)) {
            try {
                $competence->removeUser($user);
                $entityManager->flush();

                $request->getSession()->getFlashBag()->add('success', '✅ Compétence supprimée.');
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('danger', '❌ Une erreur est survenue lors de la suppression de la compétence.');
            }
        }

        return $this->redirectToRoute('app_utilisateur_competences_infos', ['userId' => $userId]);
    }

    /**
     * Ajout information formation utilisateur connecté
     */

    #Utilisateur/ajout/information/formation, permet de renseigner les formations enregistrer en base de donnée
    #[Route('/utilisateur/{userName}/ajout/informations/{userId}/formation', name: 'app_utilisateur_formation_add')]
    public function utilisateurAddFormation(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $formationRepository = $entityManager->getRepository(Formation::class);

        $formation = new Formation();
        $formationForm = $this->createForm(FormationType::class, $formation);

        $formationForm->handleRequest($request);
        if ($formationForm->isSubmitted() && $formationForm->isValid()) {
            try {
                $formation->setFormationUser($user);

                $entityManager->persist($formation);
                $entityManager->flush();

                $request->getSession()->getFlashBag()->add('success', '✅ Formation ajoutée.');

                return $this->redirectToRoute('app_utilisateur');
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('danger', '❌ Une erreur est survenue lors de l\'ajout de la formation.');
            }
        }

        return $this->render('index/dataForm.html.twig', [
            'title' => 'Profil Utilisateur',
            'formationFormName' => 'Formulaire d\'ajout de Formation',
            'user' => $user,
            'year' => new \DateTime('now'),
            'formationForm' => $formationForm->createView(),
        ]);
    }

    /**
     * Modification information formation utilisateur connecté
     */

    #Utilisateur/modifier/information/formation, permet de modifier les formations enregistrer en base de donnée
    #[Route('/utilisateur/{userName}/modifier/informations/{formationId}/formation/{formationTitle}', name: 'app_utilisateur_formation_update')]
    public function utilisateurUpdateFormation(int $formationId, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $formationRepository = $entityManager->getRepository(Formation::class);

        // Récupérer la formation à modifier
        $formation = $formationRepository->find($formationId);

        // Vérifier si la formation existe
        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée.');
        }

        // Vérifier si l'utilisateur est autorisé à modifier la formation
        if ($formation->getFormationUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette formation.');
        }

        $formationForm = $this->createForm(FormationType::class, $formation);

        $formationForm->handleRequest($request);
        if ($formationForm->isSubmitted() && $formationForm->isValid()) {
            try {
                $entityManager->flush();

                $request->getSession()->getFlashBag()->add('success', '✅ Formation modifiée.');

                return $this->redirectToRoute('app_utilisateur');
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('danger', '❌ Une erreur est survenue lors de la modification de la formation.');
            }
        }

        return $this->render('index/dataForm.html.twig', [
            'title' => 'Profil Utilisateur',
            'formationFormName' => 'Formulaire de modification de Formation',
            'user' => $user,
            'year' => new \DateTime('now'),
            'formationForm' => $formationForm->createView(),
        ]);
    }

    /**
     * Supprimer information formation utilisateur connecté
     */

    #Utilisateur/supprimer/information/formation, permet de supprimer les formations enregistrées en base de données
    #[Route('/utilisateur/{userName}/supprimer/informations/{formationId}/formation/{formationTitle}', name: 'app_utilisateur_formation_delete')]
    public function utilisateurDeleteFormation(int $formationId, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $formationRepository = $entityManager->getRepository(Formation::class);

        // Récupérer la formation à supprimer
        $formation = $formationRepository->find($formationId);

        // Vérifier si la formation existe
        if (!$formation) {
            $request->getSession()->getFlashBag()->add('danger', '❌ Formation non trouvée.');
        }

        // Vérifier si l'utilisateur est autorisé à supprimer la formation
        if ($formation->getFormationUser() !== $user) {
            $request->getSession()->getFlashBag()->add('danger', '🛑 Vous n\'êtes pas autorisé à supprimer cette formation.');
        }

        try {
            // Supprimer la formation
            $entityManager->remove($formation);
            $entityManager->flush();

            $request->getSession()->getFlashBag()->add('success', '✅ Formation supprimée.');
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add('danger', '❌ Une erreur est survenue lors de la suppression de la formation.');
        }

        return $this->redirectToRoute('app_utilisateur');
    }

    /**
     * Ajout information expérience utilisateur connecté
     */

    #Utilisateur/ajout/information/experience, permet de renseigner les experience à enregistrer en base de donnée
    #[Route('/utilisateur/{userName}/ajout/informations/{userId}/experience', name: 'app_utilisateur_experience_add')]
    public function utilisateurAddExperience(int $userId, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $experienceRepository = $entityManager->getRepository(Experience::class);

        $experience = new Experience();
        $experienceForm = $this->createForm(ExperienceType::class, $experience);

        $experienceForm->handleRequest($request);
        if ($experienceForm->isSubmitted() && $experienceForm->isValid()) {
            try {
                $experience->setUser($user);

                $entityManager->persist($experience);
                $entityManager->flush();

                $request->getSession()->getFlashBag()->add('success', '✅ Expérience ajoutée.');

                return $this->redirectToRoute('app_utilisateur');
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('danger', '❌ Une erreur est survenue lors de l\'ajout de l\expérience.');
            }
        }

        return $this->render('index/dataForm.html.twig', [
            'title' => 'Profil Utilisateur',
            'experienceFormName' => 'Formulaire d\'ajout d\'Expérience',
            'user' => $user,
            'year' => new \DateTime('now'),
            'experienceForm' => $experienceForm->createView(),
        ]);
    }

    /**
     * Modification information expérience utilisateur connecté
     */

    #Utilisateur/modifier/information/experience, permet de modifier les experiences enregistrer en base de donnée
    #[Route('/utilisateur/{userName}/modifier/informations/{experienceId}/experience/{experienceTitle}', name: 'app_utilisateur_experience_update')]
    public function utilisateurUpdateExperience(int $experienceId, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $experienceRepository = $entityManager->getRepository(Experience::class);

        // Récupérer la formation à modifier
        $experience = $experienceRepository->find($experienceId);

        // Vérifier si la formation existe
        if (!$experience) {
            throw $this->createNotFoundException('Expérience non trouvée.');
        }

        // Vérifier si l'utilisateur est autorisé à modifier la formation
        if ($experience->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette expérience.');
        }

        $experienceForm = $this->createForm(ExperienceType::class, $experience);

        $experienceForm->handleRequest($request);
        if ($experienceForm->isSubmitted() && $experienceForm->isValid()) {
            try {
                $entityManager->flush();

                $request->getSession()->getFlashBag()->add('success', '✅ Expérience correctement modifiée.');

                return $this->redirectToRoute('app_utilisateur');
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('danger', '❌ Une erreur est survenue lors de la modification de l\'expérience.');
            }
        }

        return $this->render('index/dataForm.html.twig', [
            'title' => 'Profil Utilisateur',
            'experienceFormName' => 'Formulaire de modification d\'Expérience',
            'user' => $user,
            'year' => new \DateTime('now'),
            'experienceForm' => $experienceForm->createView(),
        ]);
    }

    /**
     * Supprimer information formation utilisateur connecté
     */

    #Utilisateur/supprimer/information/formation, permet de supprimer les formations enregistrées en base de données
    #[Route('/utilisateur/{userName}/supprimer/informations/{experienceId}/experience/{experienceTitle}', name: 'app_utilisateur_experience_delete')]
    public function utilisateurDeleteExperience(int $experienceId, Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $experienceRepository = $entityManager->getRepository(Experience::class);

        // Récupérer la formation à supprimer
        $experience = $experienceRepository->find($experienceId);

        // Vérifier si la formation existe
        if (!$experience) {
            $request->getSession()->getFlashBag()->add('danger', '❌ Expérience non trouvée.');
        }

        // Vérifier si l'utilisateur est autorisé à supprimer la formation
        if ($experience->getUser() !== $user) {
            $request->getSession()->getFlashBag()->add('warning', '🛑 Vous n\'êtes pas autorisé à supprimer cette expérience.');
        }

        try {
            // Supprimer la formation
            $entityManager->remove($experience);
            $entityManager->flush();

            $request->getSession()->getFlashBag()->add('success', '✅ Expérience supprimée.');
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add('danger', '❌ Une erreur est survenue lors de la suppression de l\'expérience.');
        }

        return $this->redirectToRoute('app_utilisateur');
    }

    /**
     * Afficher et rechercher utilisateur enregistré
     */

    #Membres, affiche tous les membres et permet d'en rechercher via une chaîne de caractères ou au travers de filtres
    #[Route('/membre', name: 'app_membre')]
    public function rechercher(Request $request, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);

        $skillsRepository = $entityManager->getRepository(Skills::class);
        $competences = $skillsRepository->findAll();

        $searchTerm = $request->query->get('search');

        $queryBuilder = $userRepository->createQueryBuilder('u');

        if ($searchTerm) {
            $queryBuilder->leftJoin('u.skills', 's') // Rejoindre la table Skills
                ->andWhere(
                    $queryBuilder->expr()->orX( // Utiliser une condition OR pour rechercher dans plusieurs champs
                        $queryBuilder->expr()->like('u.username', ':searchTerm'),
                        $queryBuilder->expr()->like('u.lastname', ':searchTerm'),
                        $queryBuilder->expr()->like('u.firstname', ':searchTerm'),
                        $queryBuilder->expr()->like('u.job_title', ':searchTerm'),
                        $queryBuilder->expr()->like('u.localisation', ':searchTerm'),
                        $queryBuilder->expr()->like('u.statut', ':searchTerm'),
                        $queryBuilder->expr()->like('s.name', ':searchTerm')
                    )
                )
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        try {
            $users = $queryBuilder->getQuery()->getResult();
        } catch (\Exception $e) {
            $session->getFlashBag()->add('danger', '❌ Une erreur s\'est produite lors de la recherche des membres.');
            return $this->redirectToRoute('app_membre'); // Rediriger vers une autre page
        }

        return $this->render('index/membre.html.twig', [
            'title' => 'Membre',
            'user' => $user,
            'users' => $users,
            'competences' => $competences,
        ]);
    }

    /**
     * Afficher information utilisateur enregistré
     */

    #Membre/informations, permet d'afficher les informations d'un membre
    #[Route('/membre/informations/{userId}/{userName}', name: 'app_membre_infos')]
    public function membreShowInfos(int $userId, string $userName, ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $membre = $userRepository->find($userId);

        if (!$membre) {
            $request->getSession()->getFlashBag()->add('danger', '🛑 Membre non trouvé.');
            return $this->redirectToRoute('app_membre'); // Rediriger vers une autre page
        }

        if ($membre->getUsername() !== $userName) {
            $request->getSession()->getFlashBag()->add('danger', '🛑 Membre non trouvé.');
            return $this->redirectToRoute('app_membre'); // Rediriger vers une autre page
        }

        return $this->render('index/showMembre.html.twig', [
            'title' => 'Profil Utilisateur de',
            'membre' => $membre,
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }

    /**
     * À propos
     */
    #[Route('/à propos', name: 'app_a_propos')]
    public function appApropos(): Response
    {
        $user = $this->getUser();

        return $this->render('index/aPropos.html.twig', [
            'title' => 'À propos',
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }

    /**
     * Conditions Générales d'utilisation
     */
    #[Route('/condition-generale-utilisation', name: 'app_cgu')]
    public function appCgu(): Response
    {
        $user = $this->getUser();

        return $this->render('index/cgu.html.twig', [
            'title' => 'Conditions Générales d\'utilisation',
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }

    /**
     * Politique de Confidentialité
     */
    #[Route('/politique-confidentialite', name: 'app_politique_confidentialite')]
    public function appPolitiqueConfidentialite(): Response
    {
        $user = $this->getUser();

        return $this->render('index/politiqueConfidentialite.html.twig', [
            'title' => 'Politique de Confidentialité',
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }

    /**
     * Mentions légales
     */
    #[Route('/mentions-legales', name: 'app_mentions_legales')]
    public function appMentionsLegales(): Response
    {
        $user = $this->getUser();

        return $this->render('index/mentionsLegales.html.twig', [
            'title' => 'Mentions légales',
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }


    /**
     * Error 404
     */
    #[Route('/', name: 'app_error_404')]
    public function appError404(): Response
    {
        $user = $this->getUser();

        return $this->render('index/404.html.twig', [
            'title' => '404 error',
            'user' => $user,
            'year' => new \DateTime('now'),
        ]);
    }

    /**
     * Afficher les error_log
     */

    #MError
    #[Route('/error', name: 'app_error')]
    public function appError(ManagerRegistry $doctrine, Request $request): Response
    {
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $userRepository = $entityManager->getRepository(Users::class);
        $errors = phpinfo();

        return $this->render('index/test.html.twig', [
            'title' => 'Profil Utilisateur de',
            'user' => $user,
            'errors' => $errors,
            'year' => new \DateTime('now'),
        ]);
    }
}
