<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin/user', name: 'admin_user_')]
class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Show all users
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    // View a single user details
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'user' => $user,
        ]);
    }

    // Edit a user
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, $id): Response {
        $user = $this->entityManager->getRepository(User::class)->find($id);
    
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }
    
        $formUpdate = $this->createForm(UserType::class, $user);
        $formUpdate->handleRequest($request);
    
        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'User updated successfully.');
            return $this->redirectToRoute('admin_user_index');
        }

        $users = $this->entityManager->getRepository(User::class)->findAll();
    
        return $this->render('admin/user/index.html.twig', [
            'user_edit' => $user,
            'formUpdate' => $formUpdate->createView(),
            'users' => $users,
        ]);
    }
    
    // Delete a user
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'User deleted successfully.');
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
