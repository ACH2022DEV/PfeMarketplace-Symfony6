<?php

namespace App\Entity;

use App\Repository\ApiProductClickRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiProductClickRepository::class)]
class ApiProductClick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $ipTraveler = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $ipLocation = null;

    #[ORM\ManyToOne(inversedBy: 'apiProductClicks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Traveler $traveler = null;

    #[ORM\ManyToOne(inversedBy: 'apiProductClicks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ApiProduct $apiProduct = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIpTraveler(): ?string
    {
        return $this->ipTraveler;
    }

    public function setIpTraveler(?string $ipTraveler): self
    {
        $this->ipTraveler = $ipTraveler;

        return $this;
    }

    public function getIpLocation(): ?string
    {
        return $this->ipLocation;
    }

    public function setIpLocation(?string $ipLocation): self
    {
        $this->ipLocation = $ipLocation;

        return $this;
    }

    public function getTraveler(): ?Traveler
    {
        return $this->traveler;
    }

    public function setTraveler(?Traveler $traveler): self
    {
        $this->traveler = $traveler;

        return $this;
    }

    public function getApiProduct(): ?ApiProduct
    {
        return $this->apiProduct;
    }

    public function setApiProduct(?ApiProduct $apiProduct): self
    {
        $this->apiProduct = $apiProduct;

        return $this;
    }

}
