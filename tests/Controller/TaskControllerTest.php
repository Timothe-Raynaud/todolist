<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TaskControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private KernelBrowser $client;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'aaaa']);
        $this->assertTrue($this->user instanceof User, 'Aucun utilisateur trouvé');
        $this->client->loginUser($this->user);
    }

    public function testList(): void
    {
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();

        $tasks = $this->entityManager->getRepository(Task::class)->findAll();
        $this->assertSelectorCount(count($tasks),'.card');

        $deleteButton = $crawler->selectButton('Supprimer');
        $this->assertCount(10, $deleteButton);
    }

    public function testCreate(): void
    {
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Nouvelle tâche',
            'task[content]' => 'Description de la tâche',
        ]);

        $this->client->submit($form);

        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['title' => 'Nouvelle tâche']);
        $this->assertNotNull($task, 'La tâche n\'a pas été enregistrée en base de données');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Le statut de la réponse n\'est pas une redirection.');
        $this->client->followRedirect();
        $this->assertStringContainsString('/tasks', $this->client->getRequest()->getUri(), 'La redirection vers la liste des tâches a échoué.');
        $flashMessage = $this->client->getCrawler()->filter('div.alert-success')->text();
        $this->assertStringContainsString('La tâche a été bien été ajoutée.', $flashMessage, 'Le message flash attendu n\'a pas été trouvé.');

        if ($task) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
        }
    }

    public function testEdit(): void
    {
        $task = new Task();
        $task->setTitle('Tâche originale');
        $task->setContent('Contenu original');
        $task->setUser($this->user);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $taskId = $task->getId();
        $crawler = $this->client->request('GET', "/tasks/{$taskId}/edit");

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Tâche modifiée',
            'task[content]' => 'Contenu modifié',
        ]);

        $this->client->submit($form);

        $updatedTask = $this->entityManager->getRepository(Task::class)->find($taskId);
        $this->assertEquals('Tâche modifiée', $updatedTask->getTitle(), 'Le titre de la tâche n\'a pas été modifié.');
        $this->assertEquals('Contenu modifié', $updatedTask->getContent(), 'Le contenu de la tâche n\'a pas été modifié.');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Le statut de la réponse n\'est pas une redirection.');
        $this->client->followRedirect();
        $this->assertStringContainsString('/tasks', $this->client->getRequest()->getUri(), 'La redirection vers la liste des tâches a échoué.');
        $flashMessage = $this->client->getCrawler()->filter('div.alert-success')->text();
        $this->assertStringContainsString('La tâche a bien été modifiée.', $flashMessage, 'Le message flash attendu n\'a pas été trouvé.');

        $this->entityManager->remove($updatedTask);
        $this->entityManager->flush();
    }

    public function testToggleTask(): void
    {
        $task = new Task();
        $task->setTitle('Tâche à basculer');
        $task->setContent('Contenu de la tâche');
        $task->setUser($this->user);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $taskId = $task->getId();
        $this->client->request('GET', "/tasks/{$taskId}/toggle");

        $this->entityManager->refresh($task);

        $this->assertTrue($task->isDone(), 'La tâche n\'a pas été marquée comme faite.');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Le statut de la réponse n\'est pas une redirection.');
        $this->client->followRedirect();
        $this->assertStringContainsString('/tasks', $this->client->getRequest()->getUri(), 'La redirection vers la liste des tâches a échoué.');
        $flashMessage = $this->client->getCrawler()->filter('div.alert-success')->text();
        $this->assertStringContainsString(sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()), $flashMessage, 'Le message flash attendu n\'a pas été trouvé.');

        $task = $this->entityManager->find(Task::class, $task->getId());
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function testDeleteTask(): void
    {
        $task = new Task();
        $task->setTitle('Tâche à supprimer');
        $task->setContent('Contenu de la tâche');
        $task->setUser($this->user);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $taskId = $task->getId();
        $this->client->request('GET', "/tasks/{$taskId}/delete");

        $deletedTask = $this->entityManager->getRepository(Task::class)->find($taskId);
        $this->assertNull($deletedTask, 'La tâche n\'a pas été supprimée de la base de données.');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Le statut de la réponse n\'est pas une redirection.');
        $this->client->followRedirect();
        $this->assertStringContainsString('/tasks', $this->client->getRequest()->getUri(), 'La redirection vers la liste des tâches a échoué.');
        $flashMessage = $this->client->getCrawler()->filter('div.alert-success')->text();
        $this->assertStringContainsString('La tâche a bien été supprimée.', $flashMessage, 'Le message flash attendu n\'a pas été trouvé.');
    }
}