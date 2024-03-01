<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\TaskFixture;
use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtureTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $container = self::getContainer();

        $this->entityManager = $container->get('doctrine')
            ->getManager();

        $purger = new ORMPurger($this->entityManager);
        $purger->purge();

        $passwordHasher = $container->get(UserPasswordHasherInterface::class);
        $fixture = new UserFixture($passwordHasher);
        $fixture->load($this->entityManager);

        $fixture = new TaskFixture($container->get(UserRepository::class));
        $fixture->load($this->entityManager);
    }


    public function testUserFixture(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findAll();
        $this->assertCount(2, $user);

        $this->assertEquals('aaaa', $user[0]->getUsername());
    }
}
