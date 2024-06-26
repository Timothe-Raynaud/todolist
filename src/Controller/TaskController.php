<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route(path: '/tasks', name: 'task_list', methods: ['GET'])]
    public function list(TaskRepository $taskRepository, Request $request) : Response
    {
        $user = $this->getUser();
        if (!$user instanceof User){
            throw new Exception("Une erreur est survenue.");
        }

        $isDone = $request->get('isDone');
        $tasks = $taskRepository->getList($user, $isDone);

        return $this->render('app/task/list.html.twig', [
            'tasks' => $tasks
        ]);
    }

    #[Route(path: '/tasks/create', name: 'task_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em) : Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('app/task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/tasks/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('app/task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route(path: '/tasks/{id}/toggle', name: 'task_toggle', methods: ['GET'])]
    public function toggleTask(Task $task, EntityManagerInterface $em) : Response
    {
        if ($this->isGranted('task.canManageTask', $task)){
            $task->toggle(!$task->isDone());
            $em->flush();

            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        } else {
            $this->addFlash('error', ('Vous ne pouvez pas modifier cette tache.'));
        }

        return $this->redirectToRoute('task_list');
    }

    #[Route(path: '/tasks/{id}/delete', name: 'task_delete', methods: ['GET'])]
    public function deleteTask(Task $task, EntityManagerInterface $em): Response
    {
        if ($this->isGranted('task.canManageTask', $task)){
            $em->remove($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été supprimée.');
        } else {
            $this->addFlash('error', ('Vous ne pouvez pas supprimer cette tache.'));
        }

        return $this->redirectToRoute('task_list');
    }
}
