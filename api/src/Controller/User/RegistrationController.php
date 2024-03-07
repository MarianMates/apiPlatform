<?php

declare(strict_types=1);

namespace App\Controller\User;

use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        // validate email and password
        if (!isset($data['email']) || !isset($data['password'])) {
            return new Response('Email and password are required', Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_USER']); // Default role
            $password = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($password);

            $this->validator->validate($user);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (ValidationException) {
            return new Response('Validation error', Response::HTTP_BAD_REQUEST);
        } catch (Exception) {
            return new Response('Error creating user', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response(sprintf('User %s successfully created', $user->getUserIdentifier()));
    }
}
