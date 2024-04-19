<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(readonly UserRepository $userRepository){}

    public function load(ObjectManager $manager): void
    {
        $user = $this->userRepository->findOneBy(['username' => 'anonyme']);
        for ($i=1; $i<=10; $i++){
            $task = new Task;
            $task->setContent('Fixtures Task ' . $i);
            $task->setTitle('Task ' . $i);
            $task->setUser($user);

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
