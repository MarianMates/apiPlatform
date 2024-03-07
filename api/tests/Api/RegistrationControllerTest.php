<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $entityManager->getConnection()->beginTransaction();

        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
            'password' => 'TestPassword123!',
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('User test@example.com successfully created', $client->getResponse()->getContent());

        // Rollback changes
        $entityManager->getConnection()->rollBack();
    }

    public function testRegisterBadRequest(): void
    {
        $client = static::createClient();

        // Test missing email
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'password' => 'TestPassword123!',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('Email and password are required', $client->getResponse()->getContent());

        // Test missing password
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('Email and password are required', $client->getResponse()->getContent());

        // Test malformed email
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test.com',
            'password' => 'TestPassword123!',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('Validation error', $client->getResponse()->getContent());
    }
}
