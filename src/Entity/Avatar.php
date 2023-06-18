<?php

namespace App\Entity;

use App\Repository\AvatarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvatarRepository::class)]
class Avatar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Avatar_name = null;

    #[ORM\OneToOne(inversedBy: 'avatar', cascade: ['persist', 'remove'])]
    private ?Users $user_avatar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvatarName(): ?string
    {
        return $this->Avatar_name;
    }

    public function setAvatarName(?string $Avatar_name): self
    {
        $this->Avatar_name = $Avatar_name;

        return $this;
    }

    public function getUserAvatar(): ?Users
    {
        return $this->user_avatar;
    }

    public function setUserAvatar(?Users $user_avatar): self
    {
        $this->user_avatar = $user_avatar;

        return $this;
    }
}
