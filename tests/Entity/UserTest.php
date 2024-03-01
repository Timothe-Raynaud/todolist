<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserTest extends KernelTestCase
{
    private User $user;
    private mixed $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get('validator');

        $this->user = new User();
    }

    public function testUsernameCanBeSetAndRetrieved(): void
    {
        $username = 'testUser';
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUsername());
    }

    public function testEmailCanBeSetAndRetrieved(): void
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    public function testPasswordCanBeSetAndRetrieved(): void
    {
        $password = 'securePassword';
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    public function testValidationConstraints(): void
    {
        $invalidUser = new User();
        $violations = $this->validator->validate($invalidUser);

        $this->assertGreaterThan(0, count($violations));

        foreach ($violations as $violation) {
            $this->assertInstanceOf(NotBlank::class, $violation->getConstraint());
        }
    }

    public function testGetRoles(): void
    {
        $this->user->setRoles([User::ROLE_ADMIN]);
        $this->assertEquals(['ROLE_ADMIN'], $this->user->getRoles());
    }

    public function testGetUserIdentifier(): void
    {
        $username = 'testUser';
        $this->user->setUsername($username);
        $this->assertEquals($username, $this->user->getUserIdentifier());
    }

    public function testInvalidEmailValidation(): void
    {
        $userWithInvalidEmail = new User();
        $userWithInvalidEmail->setEmail('invalid-email');

        $violations = $this->validator->validate($userWithInvalidEmail);

        $this->assertGreaterThan(0, count($violations));

        $emailViolationFound = false;
        foreach ($violations as $violation) {
            if ($violation->getPropertyPath() === 'email' && $violation->getConstraint() instanceof Email) {
                $emailViolationFound = true;
                break;
            }
        }

        $this->assertTrue($emailViolationFound, "Aucune violation de contrainte Email trouvée pour un email invalide.");
    }

    public function testEraseCredentialsDoesNotChangeState(): void
    {
        $user = new User();
        $user->setUsername('TestCredentialUser');
        $user->setPassword('TestCredentialPassword');
        $user->setEmail('testCredential@example.com');
        $user->setRoles([User::ROLE_USER]);

        // État avant eraseCredentials
        $usernameBefore = $user->getUsername();
        $passwordBefore = $user->getPassword();
        $emailBefore = $user->getEmail();
        $rolesBefore = $user->getRoles();

        // Appel de eraseCredentials
        $user->eraseCredentials();

        // Vérifier que l'état n'a pas changé
        $this->assertEquals($usernameBefore, $user->getUsername());
        $this->assertEquals($passwordBefore, $user->getPassword());
        $this->assertEquals($emailBefore, $user->getEmail());
        $this->assertEquals($rolesBefore, $user->getRoles());
    }
}