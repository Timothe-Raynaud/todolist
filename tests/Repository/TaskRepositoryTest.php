<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private const TITLE = 'Task fo test';
    private const CONTENT = 'Content of the test task';
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->taskRepository = $container->get(TaskRepository::class);
    }

    public function testSaveAndFindTask(): void
    {
        $task = new Task();
        $task->setTitle(self::TITLE);
        $task->setContent(self::CONTENT);

        $this->taskRepository->save($task, true);

        $foundTask = $this->taskRepository->find($task->getId());
        $this->assertNotNull($foundTask);
        $this->assertEquals(self::TITLE, $foundTask->getTitle());
        $this->assertEquals(self::CONTENT, $foundTask->getContent());
    }

    public function testRemoveTask(): void
    {
        $task = $this->taskRepository->findOneBy(['title' => self::TITLE]);
        // Get id of object before deletion
        $id = $task->getId();
        $this->taskRepository->remove($task, true);

        $foundTask = $this->taskRepository->find($id);
        $this->assertNull($foundTask);
    }
}