<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly ParameterBagInterface $parameterBag){}

    public function load(ObjectManager $manager): void
    {
        $admin = new User;
        $admin->setUsername('aaaa');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'testing'));
        $admin->setRoles([User::ROLE_ADMIN]);
        $admin->setEmail('test@test.com');

        $manager->persist($admin);
        $manager->flush();

        $user = new User;
        $user->setUsername($this->parameterBag->get('anonyme_user'));
        $user->setPassword($this->passwordHasher->hashPassword($user, 'testing'));
        $user->setRoles([User::ROLE_USER]);
        $user->setEmail('anonyme@test.com');

        $manager->persist($user);
        $manager->flush();
    }
}
