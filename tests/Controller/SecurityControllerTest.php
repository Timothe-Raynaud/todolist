<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
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
}