<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User;
        $user->setUsername('aaaa');
        $user->setPassword('testing');
        $user->setEmail('test@test.com');

        $manager->persist($user);


        $manager->flush();
    }
}
