<?php

namespace App\Events;

use App\Entity\Seller;
use Symfony\Contracts\EventDispatcher\Event;

class SellerCreatedEvent extends Event
{
    const CREATE_SELLER_EVENT = 'seller.add';

    public function __construct(private Seller $seller,private $password)
    {

    }

    public function getSeller(){
        return $this->seller;
    }

    public function getPlainPassword(){
        return $this->password;
    }
}