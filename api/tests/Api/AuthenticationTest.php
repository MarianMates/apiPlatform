<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthenticationTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testLogin(): void
    {
        self::bootKernel();

        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy([]);

        $response = $client->request('POST', '/auth', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['email' => $user->getEmail(), 'password' => 'password123'],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/greetings');
        $this->assertResponseStatusCodeSame(401);

        // test authorized
        $client->request('GET', '/greetings', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}