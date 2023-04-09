<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: SellerRepository::class)]
#[Vich\Uploadable]

class Seller
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column ]
    #[Groups(['Seller','SellerOffers'])]

    private ?int $id = null;

    #[ORM\Column(length: 45)]
    #[Groups(['Seller','SellerOffers'])]

    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Seller','SellerOffers'])]

    private ?string $website = null;

    #[ORM\Column(length: 255)]
    #[Groups(['Seller','SellerOffers'])]

    private ?string $address = null;

    #[ORM\ManyToOne(inversedBy: 'sellers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Seller','SellerOffers'])]

    private ?City $city = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Seller','SellerOffers'])]
    private ?User $user = null;
    #[ORM\OneToOne(inversedBy: 'seller', cascade: ['persist', 'remove'])]
    #[Groups(['Seller','SellerOffers'])]
    private ?Api $api = null;
    #[ORM\Column(type: 'string')]
    #[Groups(['Seller','SellerOffers'])]
    private $brochureFilename;

    public function getBrochureFilename()
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename($brochureFilename)
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: SellerOffer::class, cascade: ['persist', 'remove'])]
    #[Groups(['Seller'])]
    private Collection $sellerOffers;

    public function __construct()
    {
        $this->sellerOffers = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getApi(): ?Api
    {
        return $this->api;
    }

    public function setApi(?Api $api): self
    {
        $this->api = $api;

        return $this;
    }

    /**
     * @return Collection<int, SellerOffer>
     */
    public function getSellerOffers(): Collection
    {
        return $this->sellerOffers;
    }

    public function addSellerOffer(SellerOffer $sellerOffer): self
    {
        if (!$this->sellerOffers->contains($sellerOffer)) {
            $this->sellerOffers->add($sellerOffer);
            $sellerOffer->setSeller($this);
        }

        return $this;
    }

    public function removeSellerOffer(SellerOffer $sellerOffer): self
    {
        if ($this->sellerOffers->removeElement($sellerOffer)) {
            // set the owning side to null (unless already changed)
            if ($sellerOffer->getSeller() === $this) {
                $sellerOffer->setSeller(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->name;
    }
}
