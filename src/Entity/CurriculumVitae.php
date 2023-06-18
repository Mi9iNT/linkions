<?php

namespace App\Entity;

use App\Repository\CurriculumVitaeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurriculumVitaeRepository::class)]
class CurriculumVitae
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $curriculum_name = null;

    #[ORM\OneToOne(inversedBy: 'curriculumVitae', cascade: ['persist', 'remove'])]
    private ?Users $curriculum_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurriculumName(): ?string
    {
        return $this->curriculum_name;
    }

    public function setCurriculumName(?string $curriculum_name): self
    {
        $this->curriculum_name = $curriculum_name;

        return $this;
    }

    public function getCurriculumUser(): ?Users
    {
        return $this->curriculum_user;
    }

    public function setCurriculumUser(?Users $curriculum_user): self
    {
        $this->curriculum_user = $curriculum_user;

        return $this;
    }
}
