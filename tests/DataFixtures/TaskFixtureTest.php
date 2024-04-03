<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\TaskFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TaskFixtureTest extends KernelTestCase
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
        $parameterBag = $container->get(ParameterBagInterface::class);
        $fixture = new UserFixture($passwordHasher, $parameterBag);
        $fixture->load($this->entityManager);

        $fixture = new TaskFixture($container->get(UserRepository::class));
        $fixture->load($this->entityManager);
    }


    public function testTricksFixture(): void
    {
        $task = $this->entityManager->getRepository(Task::class)->findAll();
        $this->assertCount(10, $task);

        $this->assertEquals('Task 1', $task[0]->getTitle());
    }
}
