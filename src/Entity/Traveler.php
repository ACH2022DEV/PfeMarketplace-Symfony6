<?php

namespace App\Entity;

use App\Repository\TravelerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TravelerRepository::class)]
class Traveler
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $firstName = null;

    #[ORM\Column(length: 45)]
    private ?string $lastName = null;

    #[ORM\Column(length: 45)]
    private ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'travelers')]
    private ?City $city = null;


    #[ORM\OneToMany(mappedBy: 'traveler_idTraveler', targetEntity: ApiProductClick::class)]
    private Collection $apiProductClicks;

    public function __construct()
    {
        $this->apiProductClicks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    /**
     * @return Collection<int, ApiProductClick>
     */
    public function getApiProductClicks(): Collection
    {
        return $this->apiProductClicks;
    }

    public function addApiProductClick(ApiProductClick $apiProductClick): self
    {
        if (!$this->apiProductClicks->contains($apiProductClick)) {
            $this->apiProductClicks->add($apiProductClick);
            $apiProductClick->setTraveler($this);
        }

        return $this;
    }

    public function removeApiProductClick(ApiProductClick $apiProductClick): self
    {
        if ($this->apiProductClicks->removeElement($apiProductClick)) {
            // set the owning side to null (unless already changed)
            if ($apiProductClick->getTraveler() === $this) {
                $apiProductClick->setTraveler(null);
            }
        }

        return $this;
    }
}
