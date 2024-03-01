<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private const USERNAME = 'TestUser';
    private const PASSWORD = 'TestPassword';
    private const EMAIL = 'testUser@example.com';
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testSaveAndFindUser(): void
    {
        $user = new User();
        $user->setUsername(self::USERNAME);
        $user->setPassword(self::PASSWORD);
        $user->setEmail(self::EMAIL);
        $user->setRoles([User::ROLE_USER]);
        $this->userRepository->save($user, true);

        $foundUser = $this->userRepository->find($user->getId());
        $this->assertNotNull($foundUser);
        $this->assertEquals(self::USERNAME, $foundUser->getUsername());
        $this->assertEquals(self::EMAIL, $foundUser->getEmail());
        $this->assertEquals([User::ROLE_USER], $foundUser->getRoles());
    }

    public function testRemoveUser(): void
    {
        $user = $this->userRepository->findOneBy(['email' => self::EMAIL]);
        // Get id of object before deletion
        $id = $user->getId();
        $this->userRepository->remove($user, true);

        $foundUser = $this->userRepository->find($id);
        $this->assertNull($foundUser);
    }
}