<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route(path: '/users', name: 'user_list', methods: ['GET'])]
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('app/user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

    #[Route(path: '/users/create', name: 'user_create', methods: ['GET', 'POST'])]
    #[IsGranted('user.canManageUser')]
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles([$form->get('roles')->getData()]);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        if($form->isSubmitted()){
            $errors = $validator->validate($user);
        }

        return $this->render('app/user/create.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors ?? null,
        ]);
    }

    #[Route(path: '/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('app/user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
