<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[UniqueEntity(['code'])]
#[UniqueEntity(['alpha3'])]
#[UniqueEntity(['name'])]
#[UniqueEntity(['phone_code'])]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 2, unique: true)]
    #[Assert\Length(min: 2, max: 2)]
    #[Assert\NotBlank]
    private $code;

    #[ORM\Column(type: 'string', length: 3, unique: true)]
    #[Assert\Length(min: 3, max: 3)]
    #[Assert\NotBlank]
    private $alpha3;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    #[Assert\Length(min: 3, max: 64)]
    #[Assert\NotBlank]
    private $name;


    #[Assert\Range(
        min: 1,
        max: 2000
    )]
    #[ORM\Column(type: 'smallint', nullable: true)]
    private $phone_code;


    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private $capital;

    #[ORM\ManyToOne(targetEntity: Continent::class, inversedBy: 'countries')]
    #[ORM\JoinColumn(name: "continent_code", referencedColumnName: 'code')]
    private $continent;

    #[ORM\Column(type: 'boolean', nullable: 'true')]
    private $active;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: City::class)]
    private $cities;

    #[ORM\ManyToOne(targetEntity: Currency::class, inversedBy: 'countries')]
    #[ORM\JoinColumn(name: "currency_code", referencedColumnName: 'code')]
    private $currency;


    public function __construct()
    {
        $this->cities = new ArrayCollection();

    }


    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCountryCode(string $country_code): self
    {
        $this->code = $country_code;

        return $this;
    }

    public function getAlpha3(): ?string
    {
        return $this->alpha3;
    }

    public function setAlpha3(string $alpha3): self
    {
        $this->alpha3 = $alpha3;

        return $this;
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

    public function getPhoneCode(): ?int
    {
        return $this->phone_code;
    }

    public function setPhoneCode(int $phone_code): self
    {
        $this->phone_code = $phone_code;

        return $this;
    }


    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(?string $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    public function getContinent(): ?Continent
    {
        return $this->continent;
    }

    public function setContinent(?Continent $continent): self
    {
        $this->continent = $continent;

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

    /**
     * @return Collection<int, City>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setCountryCode($this);
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getCountryCode() === $this) {
                $city->setCountryCode(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;
        return $this;
    }


}
