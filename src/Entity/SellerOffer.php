<?php

namespace App\Entity;

use App\Repository\SellerOfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SellerOfferRepository::class)]
class SellerOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne( cascade: ['persist','remove'],inversedBy: 'sellerOffers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offer $offer = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'sellerOffers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seller $seller = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    //ajouter une méthode pour calculer la date

    /**
     * @throws \Exception
     */
    public function getEndDate(): \DateTimeInterface
    {
        $endDate = clone $this->getStartDate();
        $endDate->add(new \DateInterval('P' . $this->offer->getNbDays() . 'D'));
        //return $endDate->format('Y-m-d H:i:s');
        return $endDate;

    }
    public function isPending(): bool
    {
        return $this->getEndDate() > new \DateTime();
    }
    //end method
}
