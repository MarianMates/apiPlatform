<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/register',
            formats: ['json' => ['application/json']],
            openapiContext: [
                'summary' => 'Register a new user',
                'description' => 'Custom registration endpoint for new users.'
            ],
            normalizationContext: ['groups' => ['user:read']],
            denormalizationContext: ['groups' => ['user:write']],
            validationContext: ['groups' => ['user:register']],
        )
    ],
    formats: ['json' => ['application/json']],
    normalizationContext: ['groups' => ['user:read']],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:write', 'user:read'])]
    #[Assert\NotBlank(groups: ['user:register'])]
    #[Assert\Email(
        message: 'The email "{{ value }}" is not a valid email.',
        groups: ['user:register'],
    )]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:write'])]
    private array $roles = [];

    #[ORM\Column]
    #[Groups(['user:write'])]
    #[Assert\NotBlank(groups: ['user:register'])]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
