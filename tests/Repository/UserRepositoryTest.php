<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
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
        $user->setUsername('TestUser');
        $user->setPassword('TestPassword');
        $user->setEmail('test@example.com');

        $this->userRepository->save($user, true);

        $foundUser = $this->userRepository->find($user->getId());
        $this->assertNotNull($foundUser);
        $this->assertEquals('TestUser', $foundUser->getUsername());
        $this->assertEquals('test@example.com', $foundUser->getEmail());
    }

    public function testRemoveUser(): void
    {
        $user = new User();
        $user->setUsername('UserToRemove');
        $user->setPassword('RemovePassword');
        $user->setEmail('remove@example.com');

        $this->userRepository->save($user, true);

        $id = $user->getId();
        $this->userRepository->remove($user, true);

        $foundUser = $this->userRepository->find($id);
        $this->assertNull($foundUser);
    }
}