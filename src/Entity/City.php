<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: 'This value should not be blank')]
    private $name;

    #[ORM\Column(type: 'decimal', precision: 16, scale: 12 , nullable: true)]
    private $latitude;

    #[ORM\Column(type: 'decimal', precision: 16, scale: 12 , nullable: true)]
    private $longitude;

    #[ORM\Column(type: 'boolean')]
    private $active;

    #[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'cities')]
    #[ORM\JoinColumn(name: "country_code", referencedColumnName: 'code')]
    private $country_code;

    #[ORM\OneToMany(mappedBy: 'city_idCity', targetEntity: Seller::class)]
    private Collection $sellers;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Traveler::class)]
    private Collection $travelers;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: MarketSubscriptionRequest::class)]
    private Collection $marketSubscriptionRequests;


    public function __construct()
    {
        $this->sellers = new ArrayCollection();
        $this->travelers = new ArrayCollection();
    }



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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCountryCode(): ?Country
    {
        return $this->country_code;
    }

    public function setCountryCode(?Country $country_code): self
    {
        $this->country_code = $country_code;

        return $this;
    }

    public function __toString(): string
    {
        return  $this->name;
    }

    /**
     * @return Collection<int, Seller>
     */
    public function getSellers(): Collection
    {
        return $this->sellers;
    }

    public function addSeller(Seller $seller): self
    {
        if (!$this->sellers->contains($seller)) {
            $this->sellers->add($seller);
            $seller->setCity($this);
        }

        return $this;
    }

    public function removeSeller(Seller $seller): self
    {
        if ($this->sellers->removeElement($seller)) {
            // set the owning side to null (unless already changed)
            if ($seller->getCity() === $this) {
                $seller->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Traveler>
     */
    public function getTravelers(): Collection
    {
        return $this->travelers;
    }

    public function addTraveler(Traveler $traveler): self
    {
        if (!$this->travelers->contains($traveler)) {
            $this->travelers->add($traveler);
            $traveler->setCity($this);
        }

        return $this;
    }

    public function removeTraveler(Traveler $traveler): self
    {
        if ($this->travelers->removeElement($traveler)) {
            // set the owning side to null (unless already changed)
            if ($traveler->getCity() === $this) {
                $traveler->setCity(null);
            }
        }

        return $this;
    }

/**
 * @return Collection<int, MarketSubscriptionRequest>
 */
public function getMarketSubscriptionRequests(): Collection
{
    return $this->marketSubscriptionRequests;
}

public function addMarketSubscriptionRequest(MarketSubscriptionRequest $marketSubscriptionRequest): self
{
    if (!$this->marketSubscriptionRequests->contains($marketSubscriptionRequest)) {
        $this->marketSubscriptionRequests->add($marketSubscriptionRequest);
        $marketSubscriptionRequest->setCity($this);
    }

    return $this;
}

public function removeMarketSubscriptionRequest(MarketSubscriptionRequest $marketSubscriptionRequest): self
{
    if ($this->marketSubscriptionRequests->removeElement($marketSubscriptionRequest)) {
        // set the owning side to null (unless already changed)
        if ($marketSubscriptionRequest->getCity() === $this) {
            $marketSubscriptionRequest->setCity(null);
        }
    }

    return $this;
}

}
