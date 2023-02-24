<?php

namespace App\Entity;

use App\Repository\MarketSubscriptionRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MarketSubscriptionRequestRepository::class)]
class MarketSubscriptionRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    private ?string $name = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    private ?string $email = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    #[Assert\Url]
    private ?string $website = null;

    #[ORM\Column(length: 45)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    private ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'marketSubscriptionRequests')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    private ?City $city = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $status = 'pending';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

}
