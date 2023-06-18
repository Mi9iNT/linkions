<?php

namespace App\Entity;

use App\Repository\VisibilityProfilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisibilityProfilRepository::class)]
class VisibilityProfil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $user_visbility = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $admin_visibility = null;

    #[ORM\OneToOne(inversedBy: 'visibilityProfil', cascade: ['persist', 'remove'])]
    private ?Users $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserVisibility(): ?string
    {
        return $this->user_visbility;
    }

    public function setUserVisibility(?string $user_visbility): self
    {
        $this->user_visbility = $user_visbility;

        return $this;
    }

    public function getAdminVisibility(): ?string
    {
        return $this->admin_visibility;
    }

    public function setAdminVisibility(?string $admin_visibility): self
    {
        $this->admin_visibility = $admin_visibility;

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
