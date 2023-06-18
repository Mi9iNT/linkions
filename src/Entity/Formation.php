<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $formation_title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $formation_centre_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $formation_duree = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $formation_date_debut = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $formation_date_fin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $formation_details = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $formation_validee = null;

    #[ORM\ManyToOne(inversedBy: 'formations')]
    private ?Users $formation_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormationTitle(): ?string
    {
        return $this->formation_title;
    }

    public function setFormationTitle(?string $formation_title): self
    {
        $this->formation_title = $formation_title;

        return $this;
    }

    public function getFormationCentreName(): ?string
    {
        return $this->formation_centre_name;
    }

    public function setFormationCentreName(?string $formation_centre_name): self
    {
        $this->formation_centre_name = $formation_centre_name;

        return $this;
    }

    public function getFormationDuree(): ?string
    {
        return $this->formation_duree;
    }

    public function setFormationDuree(?string $formation_duree): self
    {
        $this->formation_duree = $formation_duree;

        return $this;
    }

    public function getFormationDateDebut(): ?\DateTimeImmutable
    {
        return $this->formation_date_debut;
    }

    public function setFormationDateDebut(?\DateTimeImmutable $formation_date_debut): self
    {
        $this->formation_date_debut = $formation_date_debut;

        return $this;
    }

    public function getFormationDateFin(): ?\DateTimeImmutable
    {
        return $this->formation_date_fin;
    }

    public function setFormationDateFin(?\DateTimeImmutable $formation_date_fin): self
    {
        $this->formation_date_fin = $formation_date_fin;

        return $this;
    }

    public function getFormationDetails(): ?string
    {
        return $this->formation_details;
    }

    public function setFormationDetails(?string $formation_details): self
    {
        $this->formation_details = $formation_details;

        return $this;
    }

    public function getFormationValidee(): ?string
    {
        return $this->formation_validee;
    }

    public function setFormationValidee(?string $formation_validee): self
    {
        $this->formation_validee = $formation_validee;

        return $this;
    }

    public function getFormationUser(): ?Users
    {
        return $this->formation_user;
    }

    public function setFormationUser(?Users $formation_user): self
    {
        $this->formation_user = $formation_user;

        return $this;
    }
}
