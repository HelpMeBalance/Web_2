<?php
// src/Controller/AdminCommandeController.php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CommandeType;
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
    public function edit(Request $request, Commande $commande , ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update the status directly
            $commande->setStatus(1);

            // Flush changes to the database
            $this->$doctrine->getManager()->flush();

            // Redirect to the index page
            return $this->redirectToRoute('admin_commandes_index');
        }

        return $this->render('admin/commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
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
public function indexx(CommandeRepository $commandeRepository): Response
{
    // Fetch commandes data and calculate statistics
    $commandes = $commandeRepository->findAll();
    $paymentMethodData = $this->calculatePaymentMethodStatistics($commandes);

    // Pass data to Twig template
    return $this->render('admin/commande/index.html.twig', [
        'paymentMethodData' => $paymentMethodData,
        // Other data...
    ]);
}

private function calculatePaymentMethodStatistics(array $commandes): array
{
    // Calculate payment method statistics
    $paymentMethodCounts = [];
    foreach ($commandes as $commande) {
        $paymentMethod = $commande->getPaymentMethod();
        if (!isset($paymentMethodCounts[$paymentMethod])) {
            $paymentMethodCounts[$paymentMethod] = 0;
        }
        $paymentMethodCounts[$paymentMethod]++;
    }

    // Prepare data for Chart.js
    $labels = array_keys($paymentMethodCounts);
    $values = array_values($paymentMethodCounts);

    return [
        'labels' => $labels,
        'values' => $values,
    ];
}
}