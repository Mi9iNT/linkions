<?php

namespace App\Entity;

use App\Repository\ExperienceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
class Experience
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poste_name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $tache_realisee = null;

    #[ORM\Column(nullable: true)]
    private ?\DateInterval $duree_contrat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $entreprise_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $entreprise_location = null;

    #[ORM\ManyToOne(inversedBy: 'experiences')]
    private ?Users $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosteName(): ?string
    {
        return $this->poste_name;
    }

    public function setPosteName(?string $poste_name): self
    {
        $this->poste_name = $poste_name;

        return $this;
    }

    public function getTacheRealisee(): ?string
    {
        return $this->tache_realisee;
    }

    public function setTacheRealisee(?string $tache_realisee): self
    {
        $this->tache_realisee = $tache_realisee;

        return $this;
    }

    public function getDureeContrat(): ?\DateInterval
    {
        return $this->duree_contrat;
    }

    public function setDureeContrat(?\DateInterval $duree_contrat): self
    {
        $this->duree_contrat = $duree_contrat;

        return $this;
    }

    public function getEntrepriseName(): ?string
    {
        return $this->entreprise_name;
    }

    public function setEntrepriseName(?string $entreprise_name): self
    {
        $this->entreprise_name = $entreprise_name;

        return $this;
    }

    public function getEntrepriseLocation(): ?string
    {
        return $this->entreprise_location;
    }

    public function setEntrepriseLocation(?string $entreprise_location): self
    {
        $this->entreprise_location = $entreprise_location;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }
}
