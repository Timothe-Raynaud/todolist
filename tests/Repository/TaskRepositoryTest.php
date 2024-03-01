<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private const TITLE = 'Task of test';
    private const CONTENT = 'Content of the test task';
    private const USERNAME = 'TestTaskUser';
    private const PASSWORD = 'TestPassword';
    private const EMAIL = 'testTask@example.com';
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;


    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
        $this->taskRepository = $container->get(TaskRepository::class);
    }

    public function testSaveAndFindTask(): void
    {
        $user = new User;
        $user->setUsername(self::USERNAME);
        $user->setPassword(self::PASSWORD);
        $user->setEmail(self::EMAIL);
        $user->setRoles([User::ROLE_USER]);
        $this->userRepository->save($user, true);

        $task = new Task();
        $task->setTitle(self::TITLE);
        $task->setContent(self::CONTENT);
        $task->setUser($user);

        $this->taskRepository->save($task, true);

        $foundTask = $this->taskRepository->find($task->getId());
        $this->assertNotNull($foundTask);
        $this->assertEquals(self::TITLE, $foundTask->getTitle());
        $this->assertEquals(self::CONTENT, $foundTask->getContent());
        $this->assertEquals($user, $foundTask->getUser());

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