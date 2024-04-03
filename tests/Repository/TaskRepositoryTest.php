<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TaskRepositoryTest extends KernelTestCase
{
    private const TITLE = 'Task of test';
    private const CONTENT = 'Content of the test task';
    private const USERNAME = 'TestTaskUser';
    private const PASSWORD = 'TestPassword';
    private const EMAIL = 'testTask@example.com';
    private TaskRepository $taskRepository;
    private UserRepository $userRepository;
    private ParameterBagInterface $parameterBag;


    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
        $this->taskRepository = $container->get(TaskRepository::class);
        $this->parameterBag = $container->get(ParameterBagInterface::class);
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

    public function testGetList(): void
    {
        $user = $this->userRepository->findOneBy(['username' => $this->parameterBag->get('anonyme_user')]);

        $task = $this->taskRepository->getList($user, 0);
        $this->assertCount(10, $task);

        $task = $this->taskRepository->getList($user, 1);
        $this->assertCount(0, $task);
    }
}