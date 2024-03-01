<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'aaaa']);
        $this->assertTrue($user instanceof User, 'Aucun utilisateur trouvé');

        $this->client->loginUser($user);
    }

    public function testListUsers(): void
    {
        $crawler = $this->client->request('GET', '/users');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testCreateUser(): void
    {
        $crawler = $this->client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'testUser',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'testuser@example.com',
            'user[roles]' => User::ROLE_USER,
        ]);

        $this->client->submit($form);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'testUser']);
        $this->assertNotNull($user, 'L\'utilisateur n\'a pas été enregistrée en base de données');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Le statut de la réponse n\'est pas une redirection.');
        $this->client->followRedirect();
        $this->assertStringContainsString('/users', $this->client->getRequest()->getUri(), 'La redirection vers la liste des utilisateurs a échoué.');
        $flashMessage = $this->client->getCrawler()->filter('div.alert-success')->text();
        $this->assertStringContainsString('L\'utilisateur a bien été ajouté.', $flashMessage, 'Le message flash attendu n\'a pas été trouvé.');

        if ($user) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
    }

    public function testEditUser(): void
    {
        $user = new User();
        $user->setUsername('originalUser');
        $user->setPassword('originalPassword');
        $user->setEmail('original@example.com');
        $user->setRoles([User::ROLE_USER]);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/users/'.$user->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'editedUser',
            'user[password][first]' => 'newPassword',
            'user[password][second]' => 'newPassword',
            'user[email]' => 'edited@example.com',
        ]);

        $this->client->submit($form);

        $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());
        $this->assertEquals('editedUser', $updatedUser->getUsername(), 'Le username n\'a pas été modifié.');
        $this->assertEquals('edited@example.com', $updatedUser->getEmail(), 'L\'email n\'a pas été modifié.');


        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString("L'utilisateur a bien été modifié", $crawler->filter('.alert-success')->text());
        $flashMessage = $this->client->getCrawler()->filter('div.alert-success')->text();
        $this->assertStringContainsString("L'utilisateur a bien été modifié", $flashMessage, 'Le message flash attendu n\'a pas été trouvé.');

        $this->entityManager->remove($updatedUser);
        $this->entityManager->flush();
    }
}