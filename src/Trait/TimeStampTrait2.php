<?php

namespace App\Trait;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TimeStampTrait2
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $createdAt = null;
    public function getCreatedAt(): DateTime
    {
        if (is_null($this->createdAt)) {
            $this->setCreatedAt(new \DateTime('now'));
        }
        return $this->createdAt;
    }
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    #[ORM\PrePersist]

    public function onPrePersist(): void
    {
        $this->createdAt = new DateTime();
    }



}