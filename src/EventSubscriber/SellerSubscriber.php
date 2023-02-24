<?php

namespace App\EventSubscriber;

use App\Events\SellerCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SellerSubscriber implements EventSubscriberInterface
{

    public function __construct(private MailerInterface $mailer)
    {

    }

    public static function getSubscribedEvents()
    {
        return [SellerCreatedEvent::CREATE_SELLER_EVENT => ['onSellerCreatedEvent', 0]];
    }

    public function onSellerCreatedEvent(SellerCreatedEvent $event){
        $seller = $event->getSeller();
        $emailAddress = $event->getSeller()->getUser()->getEmail();
        $name = $event->getSeller()->getName();
        $email = (new Email())
            ->from('3t@example.com')
            ->to($emailAddress)
            ->subject('Your account has been created')
            ->html(sprintf('Welcome %s! Your account has been created. Your email is %s and your password is %s.',
                $event->getSeller()->getName(),
                $event->getSeller()->getUser()->getEmail(),
                $event->getPlainPassword()));

        $this->mailer->send($email);

    }

}