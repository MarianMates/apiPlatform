<?php

namespace App\EventListeners;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserPasswordHasherListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(PrePersistEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        if ($this->passwordHasher->needsRehash($entity)) {
            $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
            $entity->setPassword($hashedPassword);
        }
    }
}