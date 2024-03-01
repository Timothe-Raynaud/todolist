<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskTest extends KernelTestCase
{
    private Task $task;
    private mixed $validator;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $container = self::getContainer();

        $this->validator = $container->get('validator');
        $this->entityManager = $container->get('doctrine')
            ->getManager();

        $this->task = new Task();
    }

    public function testConstructorInitialValues(): void
    {
        $this->assertInstanceOf(DateTimeInterface::class, $this->task->getCreatedAt());
        $this->assertFalse($this->task->isDone());
    }

    public function testTitleCanBeSetAndRetrieved(): void
    {
        $title = 'Test Task';
        $this->task->setTitle($title);
        $this->assertEquals($title, $this->task->getTitle());
    }

    public function testContentCanBeSetAndRetrieved(): void
    {
        $content = 'Test content';
        $this->task->setContent($content);
        $this->assertEquals($content, $this->task->getContent());
    }

    public function testIsDoneCanBeSetAndRetrieved(): void
    {
        $this->assertFalse($this->task->isDone());
        $this->task->toggle(true);
        $this->assertTrue($this->task->isDone());
    }

    public function testUserCanBeSetAndRetrieved(): void
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        foreach ($users as $user){
            $this->task->setUser($user);
            $this->assertEquals($user, $user);
        }
    }

    public function testValidationConstraints(): void
    {
        $invalidTask = new Task();
        $invalidTask->setTitle('');
        $invalidTask->setContent('');

        $violations = $this->validator->validate($invalidTask);

        $this->assertGreaterThan(0, count($violations));

        foreach ($violations as $violation) {
            $this->assertInstanceOf(NotBlank::class, $violation->getConstraint());
        }
    }

    public function testSetCreatedAt(): void
    {
        $date = new \DateTime();
        $this->task->setCreatedAt($date);
        $this->assertEquals($date, $this->task->getCreatedAt());
    }
}
