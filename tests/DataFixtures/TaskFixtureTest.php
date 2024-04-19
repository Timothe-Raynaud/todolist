<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\TaskFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TaskFixtureTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

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
        $parameterBag = $container->get(ParameterBagInterface::class);
        $fixture = new UserFixture($passwordHasher, $parameterBag);
        $fixture->load($this->entityManager);

        $this->userRepository = $container->get(UserRepository::class);
        $fixture = new TaskFixture($this->userRepository);
        $fixture->load($this->entityManager);
    }


    public function testTricksFixture(): void
    {
        $task = $this->entityManager->getRepository(Task::class)->findAll();
        $this->assertCount(10, $task);

        $this->assertEquals('Task 1', $task[0]->getTitle());
    }

    public function testGetDependencies(): void
    {
        // Instantiate the TaskFixture
        $fixture = new TaskFixture($this->userRepository);

        // Get dependencies
        $dependencies = $fixture->getDependencies();

        // Assertions to ensure the dependencies array is correct
        $this->assertIsArray($dependencies);
        $this->assertCount(1, $dependencies);
        $this->assertEquals(expected: UserFixture::class, actual: $dependencies[0]);
    }
}
