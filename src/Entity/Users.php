<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $job_title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $phone_number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localisation = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\DateTime(
        format: "d/m/Y",
        message: "Le format de la date de naissance doit être Jour/Mois/Année (ex : 31/12/1990)."
    )]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\ManyToMany(targetEntity: Skills::class, mappedBy: 'users')]
    private Collection $skills;

    #[ORM\OneToOne(mappedBy: 'user_avatar', cascade: ['persist', 'remove'])]
    private ?Avatar $avatar = null;

    #[ORM\OneToOne(mappedBy: 'curriculum_user', cascade: ['persist', 'remove'])]
    private ?CurriculumVitae $curriculumVitae = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Experience::class)]
    private Collection $experiences;

    #[ORM\OneToMany(mappedBy: 'formation_user', targetEntity: Formation::class)]
    private Collection $formations;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?VisibilityProfil $visibilityProfil = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetMdp = null;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->experiences = new ArrayCollection();
        $this->formations = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?int $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->job_title;
    }

    public function setJobTitle(?string $job_title): self
    {
        $this->job_title = $job_title;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }


    /**
     * @return Collection<int, Skills>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skills $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
            $skill->addUser($this);
        }

        return $this;
    }

    public function removeSkill(Skills $skill): self
    {
        if ($this->skills->removeElement($skill)) {
            $skill->removeUser($this);
        }

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
        // unset the owning side of the relation if necessary
        if ($avatar === null && $this->avatar !== null) {
            $this->avatar->setUserAvatar(null);
        }

        // set the owning side of the relation if necessary
        if ($avatar !== null && $avatar->getUserAvatar() !== $this) {
            $avatar->setUserAvatar($this);
        }

        $this->avatar = $avatar;

        return $this;
    }

    public function getCurriculumVitae(): ?CurriculumVitae
    {
        return $this->curriculumVitae;
    }

    public function setCurriculumVitae(?CurriculumVitae $curriculumVitae): self
    {
        // unset the owning side of the relation if necessary
        if ($curriculumVitae === null && $this->curriculumVitae !== null) {
            $this->curriculumVitae->setCurriculumUser(null);
        }

        // set the owning side of the relation if necessary
        if ($curriculumVitae !== null && $curriculumVitae->getCurriculumUser() !== $this) {
            $curriculumVitae->setCurriculumUser($this);
        }

        $this->curriculumVitae = $curriculumVitae;

        return $this;
    }

    /**
     * @return Collection<int, Experience>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences->add($experience);
            $experience->setUser($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getUser() === $this) {
                $experience->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setFormationUser($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getFormationUser() === $this) {
                $formation->setFormationUser(null);
            }
        }

        return $this;
    }

    public function getVisibilityProfil(): ?VisibilityProfil
    {
        return $this->visibilityProfil;
    }

    public function setVisibilityProfil(?VisibilityProfil $visibilityProfil): self
    {
        // unset the owning side of the relation if necessary
        if ($visibilityProfil === null && $this->visibilityProfil !== null) {
            $this->visibilityProfil->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($visibilityProfil !== null && $visibilityProfil->getUser() !== $this) {
            $visibilityProfil->setUser($this);
        }

        $this->visibilityProfil = $visibilityProfil;

        return $this;
    }

    public function getResetMdp(): ?string
    {
        return $this->resetMdp;
    }

    public function setResetMdp(?string $resetMdp): static
    {
        $this->resetMdp = $resetMdp;

        return $this;
    }
}
