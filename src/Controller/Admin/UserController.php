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
    public function index(Request $request): Response
    {
    $searchTerm = $request->query->get('search');
    $sortField = $request->query->get('sort', 'firstname');
    $sortOrder = $request->query->get('order', 'asc');
    $perPage = 2; // You can make this a parameter or a constant

    $currentPage = (int) $request->query->get('page', 1);

    $users = $this->entityManager->getRepository(User::class)->search($searchTerm, $sortField, $sortOrder , $currentPage, $perPage);
    $totalUsers = count($users);
    $totalPages = ceil($totalUsers / $perPage);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,

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

        
    $searchTerm = $request->query->get('search');
    $sortField = $request->query->get('sort', 'firstname');
    $sortOrder = $request->query->get('order', 'asc');
    $perPage = 2; // You can make this a parameter or a constant

    $currentPage = (int) $request->query->get('page', 1);

    $users = $this->entityManager->getRepository(User::class)->search($searchTerm, $sortField, $sortOrder , $currentPage, $perPage);
    $totalUsers = count($users);
    $totalPages = ceil($totalUsers / $perPage);
    
        return $this->render('admin/user/index.html.twig', [
            'user_edit' => $user,
            'formUpdate' => $formUpdate->createView(),
            'users' => $users,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            
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

    #[Route('/{id}/ban', name: 'ban', methods: ['POST'])]
    public function ban(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        // Check if the current user has the permission to ban users
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $banDuration = $request->request->get('banDuration'); // The ban duration in days

        if (!$banDuration) {
            $this->addFlash('error', 'Ban duration must be specified.');
            return $this->redirectToRoute('admin_user_index');
        }
        $user = $entityManager->getRepository(User::class)->find($id);

        $user->setIsBanned(true);
        $user->setBanExpiresAt(new \DateTime(sprintf('+%d days', $banDuration)));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'User has been banned.');

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/unban', name: 'unban', methods: ['POST'])]
    public function unban(int $id,EntityManagerInterface $entityManager): Response
    {
        // Check if the current user has the permission to unban users
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $entityManager->getRepository(User::class)->find($id);


        $user->setIsBanned(false);
        $user->setBanExpiresAt(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'User ban has been removed.');

        return $this->redirectToRoute('admin_user_index');
    }

}
