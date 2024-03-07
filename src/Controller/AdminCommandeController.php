<?php
// src/Controller/AdminCommandeController.php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commandes')]
class AdminCommandeController extends AbstractController
{
    #[Route('/', name: 'admin_commandes_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findAll();

        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, ManagerRegistry $doctrine, CommandeRepository $commandeRepository): Response
    {
        $form = $this->createForm(Commande::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            // Update the status directly
            $commande->setStatus(1);
            // Get the EntityManager
            $entityManager = $doctrine->getManager();
            // Flush changes to the database
            $entityManager->persist($commande);
            $entityManager->flush();

            // Redirect to the index page
            return $this->redirectToRoute('admin_commandes_index');
        }
        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
            'commande' => $commande,
        ]);
    }

    #[Route("/{id}", name: "admin_commande_delete", methods: ["DELETE"])]
    public function delete(Request $request, Commande $commande): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $csrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $csrfToken)) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        // Redirect to the index page after deletion
        return $this->redirectToRoute('admin_commandes_index');
    }
    // public function indeex(CommandeRepository $commandeRepository): Response
    // {
    //     // Fetch commandes associated with the authenticated user
    //     $commandes = $commandeRepository->findBy(['user' => $this->getUser()]);

    //     // Calculate the percentages
    //     $cardPercentage = $this->calculatePaymentMethodPercentage($commandes, 'card');
    //     $cashPercentage = $this->calculatePaymentMethodPercentage($commandes, 'cash');

    //     return $this->render('admin/commande/index.html.twig', [
    //         'commandes' => $commandes,
    //         'cardPercentage' => $cardPercentage,
    //         'cashPercentage' => $cashPercentage,
    //     ]);
    // }

    // private function calculatePaymentMethodPercentage($commandes, $paymentMethod)
    // {
    //     $filteredCommandes = array_filter($commandes, function ($commande) use ($paymentMethod) {
    //         return $commande->getPaymentMethod() === $paymentMethod;
    //     });

    //     return count($filteredCommandes) / count($commandes) * 100;
    // }
}
