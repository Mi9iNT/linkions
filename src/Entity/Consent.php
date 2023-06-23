<?php

namespace App\Entity;

use App\Repository\ConsentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConsentRepository::class)]
class Consent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $consentValue = null;

    #[ORM\OneToOne(inversedBy: 'consent', cascade: ['persist', 'remove'])]
    private ?Users $userConsent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsentValue(): ?string
    {
        return $this->consentValue;
    }

    public function setConsentValue(?string $consentValue): static
    {
        $this->consentValue = $consentValue;

        return $this;
    }

    public function getUserConsent(): ?Users
    {
        return $this->userConsent;
    }

    public function setUserConsent(?Users $userConsent): static
    {
        $this->userConsent = $userConsent;

        return $this;
    }
}
