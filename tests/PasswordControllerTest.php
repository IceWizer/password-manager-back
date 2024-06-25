<?php

namespace App\Tests;

use App\Entity\Password;
use App\Entity\Share;
use App\Entity\User;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class PasswordControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        
        // Mocking the user
        $user = $this->createMock(UserInterface::class);
        
        // Mocking the EntityManager
        $em = $this->createMock(EntityManagerInterface::class);
        
        // Mocking the repository
        /** @var EntityRepository&\PHPUnit\Framework\MockObject\MockObject $repository */
        $repository = $this->createMock(EntityRepository::class);
        $em->method('getRepository')->willReturn($repository);
        
        // Mocking the findBy method
        $password = new Password();
        $repository->method('findBy')->willReturn([$password]);

        // Simulating user authentication
        $client->getContainer()->set('security.token_storage', $this->getTokenStorageMock($user));
        
        // Injecting the mocked EntityManager into the client container
        $client->getContainer()->set('doctrine.orm.entity_manager', $em);
        
        // Sending request to the route
        $client->request('GET', '/api/passwords');
        
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testIndexShared()
    {
        $client = static::createClient();

        // Mocking the user
        $user = $this->createMock(UserInterface::class);

        // Mocking the EntityManager
        $em = $this->createMock(EntityManagerInterface::class);

        // Mocking the Share repository
        /** @var EntityRepository&\PHPUnit\Framework\MockObject\MockObject $repository */
        $repository = $this->createMock(EntityRepository::class);
        $em->method('getRepository')->willReturn($repository);

        // Mocking the findBy method for Share
        $share = new Share();
        $repository->method('findBy')->willReturn([$share]);

        // Simulating user authentication
        $client->getContainer()->set('security.token_storage', $this->getTokenStorageMock($user));
        
        // Injecting the mocked EntityManager into the client container
        $client->getContainer()->set('doctrine.orm.entity_manager', $em);

        // Sending request to the route
        $client->request('GET', '/api/passwords/shared');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testShow()
    {
        $client = static::createClient();

        // Mocking the user
        $user = $this->createMock(UserInterface::class);

        // Mocking the Password entity
        $password = new Password();

        // Mocking the EntityManager
        $em = $this->createMock(EntityManagerInterface::class);

        // Mocking the Password repository
        /** @var EntityRepository&\PHPUnit\Framework\MockObject\MockObject $repository */
        $repository = $this->createMock(EntityRepository::class);
        $em->method('getRepository')->willReturn($repository);

        // Mocking the findOneBy method
        $repository->method('findOneBy')->willReturn($password);

        // Simulating user authentication
        $client->getContainer()->set('security.token_storage', $this->getTokenStorageMock($user));
        
        // Injecting the mocked EntityManager into the client container
        $client->getContainer()->set('doctrine.orm.entity_manager', $em);

        // Sending request to the route
        $client->request('GET', '/api/passwords/1');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testCreate()
    {
        $client = static::createClient();

        // Mocking the user
        $user = $this->createMock(UserInterface::class);

        // Mocking the EntityManager
        $em = $this->createMock(EntityManagerInterface::class);

        // Mocking the EncryptionService
        $encryptionService = $this->createMock(EncryptionService::class);
        $encryptionService->method('encrypt')->willReturn('encrypted_password');

        // Simulating user authentication
        $client->getContainer()->set('security.token_storage', $this->getTokenStorageMock($user));

        // Injecting the mocked EntityManager and EncryptionService into the client container
        $client->getContainer()->set('doctrine.orm.entity_manager', $em);
        $client->getContainer()->set(EncryptionService::class, $encryptionService);

        // Sending request to the route
        $client->request('POST', '/api/passwords', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'label' => 'Test Label',
            'password' => 'Test Password',
            'comment' => 'Test Comment'
        ]));

        $response = $client->getResponse();
        $this->assertSame(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testUpdate()
    {
        $client = static::createClient();

        // Mocking the user
        $user = $this->createMock(UserInterface::class);

        // Mocking the Password entity
        $password = new Password();

        // Mocking the EntityManager
        $em = $this->createMock(EntityManagerInterface::class);

        // Mocking the EncryptionService
        $encryptionService = $this->createMock(EncryptionService::class);
        $encryptionService->method('encrypt')->willReturn('encrypted_password');

        // Mocking the Password repository
        /** @var EntityRepository&\PHPUnit\Framework\MockObject\MockObject $repository */
        $repository = $this->createMock(EntityRepository::class);
        $em->method('getRepository')->willReturn($repository);

        // Mocking the findOneBy method
        $repository->method('findOneBy')->willReturn($password);

        // Simulating user authentication
        $client->getContainer()->set('security.token_storage', $this->getTokenStorageMock($user));
        
        // Injecting the mocked EntityManager and EncryptionService into the client container
        $client->getContainer()->set('doctrine.orm.entity_manager', $em);
        $client->getContainer()->set(EncryptionService::class, $encryptionService);

        // Sending request to the route
        $client->request('PUT', '/api/passwords/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'label' => 'Updated Label',
            'password' => 'Updated Password',
            'comment' => 'Updated Comment'
        ]));

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testShowPassword()
    {
        $client = static::createClient();

        // Mocking the user
        $user = $this->createMock(UserInterface::class);

        // Mocking the Password entity
        $password = new Password();

        // Mocking the EntityManager
        $em = $this->createMock(EntityManagerInterface::class);

        // Mocking the EncryptionService
        $encryptionService = $this->createMock(EncryptionService::class);
        $encryptionService->method('decrypt')->willReturn('decrypted_password');

        // Mocking the Password repository
        /** @var EntityRepository&\PHPUnit\Framework\MockObject\MockObject $repository */
        $repository = $this->createMock(EntityRepository::class);
        $em->method('getRepository')->willReturn($repository);

        // Mocking the findOneBy method
        $repository->method('findOneBy')->willReturn($password);

        // Simulating user authentication
        $client->getContainer()->set('security.token_storage', $this->getTokenStorageMock($user));
        
        // Injecting the mocked EntityManager and EncryptionService into the client container
        $client->getContainer()->set('doctrine.orm.entity_manager', $em);
        $client->getContainer()->set(EncryptionService::class, $encryptionService);

        // Sending request to the route
        $client->request('GET', '/api/passwords/1/secret-key');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    private function getTokenStorageMock($user)
    {
        $tokenStorage = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->method('getUser')->willReturn($user);
        $tokenStorage->method('getToken')->willReturn($token);

        return $tokenStorage;
    }
}
