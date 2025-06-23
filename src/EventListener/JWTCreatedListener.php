<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        // On récupère le payload (données insérées dans le token JWT)
        $data = $event->getData();

        // On y ajoute l'ID de l'utilisateur
        $data['user_id'] = method_exists($user, 'getId') ? $user->getId() : null;

        // On met à jour le payload du token
        $event->setData($data);
    }
}
