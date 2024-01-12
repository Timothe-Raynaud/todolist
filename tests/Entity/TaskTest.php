<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskTest extends KernelTestCase
{
    private Task $task;
    private $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get('validator');

        $this->task = new Task();
    }

    public function testConstructorInitialValues(): void
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->task->getCreatedAt());
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

    public function testValidationConstraints(): void
    {
        $invalidTask = new Task();
        $invalidTask->setTitle(''); // Intentionally invalid
        $invalidTask->setContent(''); // Intentionally invalid

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
