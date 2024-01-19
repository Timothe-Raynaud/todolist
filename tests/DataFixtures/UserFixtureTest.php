<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserFixtureTest extends KernelTestCase
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

        $fixture = new UserFixture();
        $fixture->load($this->entityManager);
    }


    public function testTricksFixture(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findAll();
        $this->assertCount(1, $user);

        $this->assertEquals('aaaa', $user[0]->getUsername());
    }
}
