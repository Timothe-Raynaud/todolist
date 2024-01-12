<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
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
        $task->setTitle('Test Task');
        $task->setContent('Content of the test task');

        $this->taskRepository->save($task, true);

        $foundTask = $this->taskRepository->find($task->getId());
        $this->assertNotNull($foundTask);
        $this->assertEquals('Test Task', $foundTask->getTitle());
        $this->assertEquals('Content of the test task', $foundTask->getContent());
    }

    public function testRemoveTask(): void
    {
        $task = new Task();
        $task->setTitle('Task to Remove');
        $task->setContent('This task will be removed');

        $this->taskRepository->save($task, true);

        $id = $task->getId();
        $this->taskRepository->remove($task, true);

        $foundTask = $this->taskRepository->find($id);
        $this->assertNull($foundTask);
    }
}