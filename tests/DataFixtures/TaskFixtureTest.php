<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\TaskFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Task;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskFixtureTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $purger = new ORMPurger($this->entityManager);
        $purger->purge();

        $fixture = new TaskFixture();
        $fixture->load($this->entityManager);
    }


    public function testTricksFixture(): void
    {
        $task = $this->entityManager->getRepository(Task::class)->findAll();
        $this->assertCount(10, $task);

        $this->assertEquals('Task 1', $task[0]->getTitle());
    }
}
