<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Container;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Container $container;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->container = self::getContainer();
        $this->entityManager = $this->container->get('doctrine')
            ->getManager();
    }

    public function testLogin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Test fake user
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'fakeuser',
            '_password' => 'fakepassword',
        ]);
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('Login ou mot de passe incorrect', $this->client->getResponse()->getContent());

        // Test true user
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'aaaa',
            '_password' => 'testing',
        ]);
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Le statut de la réponse n\'est pas une redirection.');
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('/', $this->client->getRequest()->getUri(), 'La redirection vers la liste des utilisateurs a échoué.');
    }

    public function testLoginCheck(): void
    {
        $crawler = $this->client->request('GET', '/login_check');
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());

    }

    public function testLogout(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'aaaa']);
        $this->assertTrue($user instanceof User, 'Aucun utilisateur trouvé');

        $this->client->loginUser($user);

        $this->client->request('GET', '/logout');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();

        $token = $this->container->get('security.token_storage')->getToken();
        $this->assertNull($token, 'Un token de session existe');
    }
}