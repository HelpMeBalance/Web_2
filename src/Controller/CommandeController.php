<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository, Security $security): Response
    {
        // Get the currently authenticated user
        $user = $security->getUser();

        // Fetch commandes associated with the authenticated user
        $commandes = $commandeRepository->findBy(['user' => $user]);

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $commande = new Commande();
    $form = $this->createForm(CommandeType::class, $commande);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Retrieve Paniers from your table (assuming you have a PanierRepository)
        $paniers = $this->getDoctrine()->getRepository(Panier::class)->findAll();

        // Add each Panier to the Commande
        foreach ($paniers as $panier) {
            $commande->addPanier($panier);
        }

        // Persist and flush the Commande
        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('commande/new.html.twig', [
        'commande' => $commande,
        'form' => $form,
        'title' => 'Commande',
        'titlepage' => 'Commande',
        'controller_name' => 'CommandeController',
        'service' => 1,
        'part' => 59,
    ]);
}

#[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
public function show(Commande $commande): Response
{
    // Fetch the associated Paniers for the Commande
    $paniers = $commande->getPaniers();

    // Calculate the sum of panier prices
    $totalPrice = 0;
    foreach ($paniers as $panier) {
        $totalPrice += $panier->getPrixTot();
    }

    return $this->render('commande/show.html.twig', [
        'commande' => $commande,
        'paniers' => $paniers,
        'totalPrice' => $totalPrice,
    ]);
}

    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}