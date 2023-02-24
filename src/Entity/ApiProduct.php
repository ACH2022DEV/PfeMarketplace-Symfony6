<?php

namespace App\Entity;

use App\Repository\ApiProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiProductRepository::class)]
class ApiProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'apiProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductType $productType = null;

    #[ORM\ManyToOne(inversedBy: 'apiProducts')]
    private ?Api $api = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $idProductFromApi = null;

    #[ORM\ManyToMany(targetEntity: SellerOffer::class)]
    private Collection $SellerOffer_has_Product;

    #[ORM\OneToMany(mappedBy: 'apiProduct', targetEntity: ApiProductClick::class)]
    private Collection $apiProductClicks;


    public function __construct()
    {
        $this->SellerOffer_has_Product = new ArrayCollection();
        $this->apiProductClicks = new ArrayCollection();

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

    public function getProductType(): ?ProductType
    {
        return $this->productType;
    }

    public function setProductType(?ProductType $productType): self
    {
        $this->productType = $productType;

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

    public function getIdProductFromApi(): ?string
    {
        return $this->idProductFromApi;
    }

    public function setIdProductFromApi(?string $idProductFromApi): self
    {
        $this->idProductFromApi = $idProductFromApi;

        return $this;
    }

    /**
     * @return Collection<int, SellerOffer>
     */
    public function getSellerOfferHasProduct(): Collection
    {
        return $this->SellerOffer_has_Product;
    }

    public function addSellerOfferHasProduct(SellerOffer $sellerOfferHasProduct): self
    {
        if (!$this->SellerOffer_has_Product->contains($sellerOfferHasProduct)) {
            $this->SellerOffer_has_Product->add($sellerOfferHasProduct);
        }

        return $this;
    }

    public function removeSellerOfferHasProduct(SellerOffer $sellerOfferHasProduct): self
    {
        $this->SellerOffer_has_Product->removeElement($sellerOfferHasProduct);

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
            $apiProductClick->setApiProduct($this);
        }

        return $this;
    }

    public function removeApiProductClick(ApiProductClick $apiProductClick): self
    {
        if ($this->apiProductClicks->removeElement($apiProductClick)) {
            // set the owning side to null (unless already changed)
            if ($apiProductClick->getApiProduct() === $this) {
                $apiProductClick->setApiProduct(null);
            }
        }

        return $this;
    }
}
