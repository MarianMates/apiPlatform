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
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('test@example.com', $client->getResponse()->getContent());

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

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('email: This value should not be blank', $client->getResponse()->getContent());

        // Test missing password
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test@example.com',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('password: This value should not be blank', $client->getResponse()->getContent());

        // Test malformed email
        $client->request('POST', '/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'test.com',
            'password' => 'TestPassword123!',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('is not a valid email', $client->getResponse()->getContent());
    }
}
