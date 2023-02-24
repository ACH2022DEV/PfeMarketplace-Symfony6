<?php

namespace App\Entity;

use App\Repository\ProductTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductTypeRepository::class)]
class ProductType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'productType', targetEntity: OfferProductType::class)]
    private Collection $offerProductTypes;

    #[ORM\OneToMany(mappedBy: 'productType_idProductType', targetEntity: ApiProduct::class)]
    private Collection $apiProducts;

    public function __construct()
    {
        $this->offerProductTypes = new ArrayCollection();
        $this->apiProducts = new ArrayCollection();
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

    /**
     * @return Collection<int, OfferProductType>
     */
    public function getOfferProductTypes(): Collection
    {
        return $this->offerProductTypes;
    }

    public function addOfferProductType(OfferProductType $offerProductType): self
    {
        if (!$this->offerProductTypes->contains($offerProductType)) {
            $this->offerProductTypes->add($offerProductType);
            $offerProductType->setProductTypeidProductType($this);
        }

        return $this;
    }

    public function removeOfferProductType(OfferProductType $offerProductType): self
    {
        if ($this->offerProductTypes->removeElement($offerProductType)) {
            // set the owning side to null (unless already changed)
            if ($offerProductType->getProductTypeidProductType() === $this) {
                $offerProductType->setProductTypeidProductType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ApiProduct>
     */
    public function getApiProducts(): Collection
    {
        return $this->apiProducts;
    }

    public function addApiProduct(ApiProduct $apiProduct): self
    {
        if (!$this->apiProducts->contains($apiProduct)) {
            $this->apiProducts->add($apiProduct);
            $apiProduct->setProductType($this);
        }

        return $this;
    }

    public function removeApiProduct(ApiProduct $apiProduct): self
    {
        if ($this->apiProducts->removeElement($apiProduct)) {
            // set the owning side to null (unless already changed)
            if ($apiProduct->getProductType() === $this) {
                $apiProduct->setProductType(null);
            }
        }

        return $this;
    }
}
