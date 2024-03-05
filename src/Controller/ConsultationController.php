<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsultationController extends AbstractController
{
    #[Route('/consultation', name: 'app_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultationRepository->findAll(),
        ]);
    }

    #[Route('consultation/new', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consultation = new Consultation();
        $consultation->setDuree(new \DateTime());
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();

            return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('consultation/new.html.twig', [
            'consultation' => $consultation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('consultation/{id}', name: 'app_consultation_show', methods: ['GET'])]
    public function show(ConsultationRepository $Crep, $id): Response
    {
        return $this->render('consultation/show.html.twig', [
            'consultation' => $Crep->find($id),            'title' => 'consultation',
            'titlepage' => 'consultation',
            'controller_name' => 'ConsultationController',
            'service' => 1,
            'part' => 59,
        ]);
    }

    #[Route('consultation/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ConsultationRepository $Crep, EntityManagerInterface $entityManager, $id): Response
    {
        $consultation = $Crep->find($id);
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();

            return $this->redirectToRoute('app_rendez_vous_confirm', ['psyid' => $consultation->getPsychiatre()->getId()]);
        }

        return $this->render('consultation/edit.html.twig', [
            'consultation' => $consultation,
            'form' => $form->createView(),
            'title' => 'consultation',
            'titlepage' => 'consultation',
            'controller_name' => 'ConsultationController',
            'service' => 1,
            'part' => 59,
        ]);
    }

    #[Route('consultation/delete/{id}', name: 'app_consultation_delete')]
    public function delete(Request $request, ConsultationRepository $Crep, EntityManagerInterface $entityManager, $id): Response
    {
        $consultation = $Crep->find($id);
        $entityManager->remove($consultation);
        $entityManager->flush();

        return $this->redirectToRoute('app_rendez_vous_confirm', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/consultation', name: 'app_consultationAdmin')]
    public function indexAdmin(RendezVousRepository $RVrep, ConsultationRepository $Crep, Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'firstname');
        $sortOrder = $request->query->get('order', 'asc');
        $rvs = $entityManager->getRepository(Consultation::class)->search($searchTerm, $sortField, $sortOrder);

        return $this->render('admin/consultation/index.html.twig', [
            "consultations"=> $rvs,
        ]);
    }

    #[Route('/admin/consultation/{id}/edit', name: 'app_consultation_edit_admin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, ConsultationRepository $Crep, RendezVousRepository $RVrep, EntityManagerInterface $entityManager, $id): Response
    {
        $consultation = $Crep->find($id);
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_consultationAdmin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/consultation/index.html.twig', [
            'cons' => $consultation,
            'form' => $form->createView(),
            "rendezvouses" => $RVrep->findall(),
            "consultations"=> $Crep->findAll(),
        ]);
    }

    #[Route('admin/consultation/delete/{id}', name: 'app_consultation_delete_admin')]
    public function deleteAdmin(Request $request, ConsultationRepository $Crep, EntityManagerInterface $entityManager, $id): Response
    {
        $consultation = $Crep->find($id);
        $entityManager->remove($consultation);
        $entityManager->flush();

        return $this->redirectToRoute('app_consultationAdmin', [], Response::HTTP_SEE_OTHER);
    }
}
