<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\RendezVous;
use App\Form\RendezVousAdminType;
use App\Form\RendezVousType;
use App\Form\StringIdType;
use App\Repository\ConsultationRepository;
use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// #[Route('/rendez/vous')]
class RendezVousController extends AbstractController
{
    #[Route('/rendez/vous/', name: 'app_rendez_vous_index', methods: ['GET'])]
    public function index(RendezVousRepository $rendezVousRepository, Request $request, EntityManagerInterface $entityManager, ConsultationRepository $Conrep): Response
    {
        return $this->render('frontClient/viewRendezVous.html.twig', [
            'consultation' => $Conrep->findAll(),
            "rendezvous" => $rendezVousRepository->findAll(),
            'title' => 'RendezVous',
            'titlepage' => 'RendezVous',
            'controller_name' => 'RendezVousController',
            'service' => 1,
            'part' => 69,
            'rendez_vouses' => $rendezVousRepository->findAll(),
        ]);
    }

    #[Route('/rendez/vous/new', name: 'app_rendez_vous_new')]
    public function new(UserRepository $Urep, Request $request, EntityManagerInterface $entityManager): Response
    {
        $rendezVou = new RendezVous();
        $rendezVou->setDateR(new \DateTime());
        $form = $this->createForm(RendezVousType::class, $rendezVou);
        $form->handleRequest($request);
        $errordate = "";

        if ($form->isSubmitted() && $form->isValid()) {
            
            $rendezVou->setStatut(false);
            //$rendezVou->setPatient($Urep->find(?????????));
            $entityManager->persist($rendezVou);
            $entityManager->flush();
            // Check if the selected date is before today
            if ($rendezVou->getDateR() < new \DateTime('today')) {
                $form->get('dateR')->addError(new \Symfony\Component\Form\FormError('Please select a date equal to or after today.'));
                $errordate = "Please select a date equal to or after today.";
                return $this->render('rendez_vous/new.html.twig', [
                    'rendez_vou' => $rendezVou,
                    'form' => $form->createView(),
                    'errordate' => $errordate,
                ]);
            }

            return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('frontClient/rendezvous.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form->createView(),
            'title' => 'RendezVous',
            'titlepage' => 'RendezVous',
            'controller_name' => 'RendezVousController',
            'service' => 1,
            'part' => 69,
            'errordate' => $errordate,
        ]);
    }
    
    #[Route('/rendez/vous/{id}', name: 'app_rendez_vous_show', methods: ['GET'])]
    public function show(RendezVousRepository $rendezVousRepository, $id): Response
    {
        return $this->render('rendez_vous/show.html.twig', [
            'rendez_vou' => $rendezVousRepository->find($id),
        ]);
    }

    #[Route('/rendez/vous/{id}/edit', name: 'app_rendez_vous_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RendezVousRepository $RVrep, EntityManagerInterface $entityManager, $id): Response
    {
        $rendezVou = $RVrep->find($id);
        $form = $this->createForm(RendezVousType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rendez_vous/edit.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rendez/vous/{id}', name: 'app_rendez_vous_delete', methods: ['POST'])]
    public function delete(Request $request, RendezVousRepository $rendezVou, EntityManagerInterface $entityManager, $id): Response
    {
            $entityManager->remove($rendezVou->find($id));
            $entityManager->flush();

        return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }

        #[Route('/rendez/vous/psy/{psyid}', name: 'app_rendez_vous_confirm')]
    public function psyConfirm($psyid, UserRepository $userRepository, RendezVousRepository $rendezVousRepository, Request $request, EntityManagerInterface $entityManager, ConsultationRepository $Conrep): Response
    {
        $consultationId = $request->request->get('consultationId');
        return $this->render('rendez_vous/psyRVConfirmation.html.twig', [
            'consultations' => $Conrep->findAll(),
            'consultationId' => $consultationId,
            "rendezvous" => $rendezVousRepository->findBy(['user' => $userRepository->findOneBy(['id' => $psyid])]),
            'title' => 'RendezVous',
            'titlepage' => 'RendezVous',
            'controller_name' => 'RendezVousController',
            'service' => 1,
            'part' => 69,
            'rendez_vouses' => $rendezVousRepository->findAll(),
        ]);
    }

    #[Route('/rendez/vous/{id}/edit/status', name: 'app_rendez_vous_edit_status')]
    public function editS(Request $request, RendezVousRepository $RVrep, EntityManagerInterface $entityManager, $id): Response
    {
        $rendezVou = $RVrep->find($id);
        $rendezVou->setStatut(true);
        $entityManager->persist($rendezVou);
        $entityManager->flush();

        $consultation = new Consultation();
        $consultation->setPsychiatre($rendezVou->getUser());
        $consultation->setPatient($rendezVou->getPatient());
        $consultation->setRendezvous($rendezVou);
        $consultation->setDuree(new \DateTime());
        $consultation->setNote('');
        $consultation->setRecommandationSuivi(false);

        $entityManager->persist($consultation);
        $entityManager->flush();

        return $this->redirectToRoute('app_rendez_vous_confirm', ['psyid'=>$rendezVou->getUser()->getId()]);
    }

    #[Route('/admin/rendez/vous', name: 'app_rendezvousAdmin')]
    public function indexAdmin(RendezVousRepository $RVrep, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/rendezvous/index.html.twig', [
            "rendezvouses" => $RVrep->findall(),  
            
        ]);
    }

    #[Route('/admin/rendez/vous/{id}/edit', name: 'app_rendez_vous_edit_admin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, RendezVousRepository $RVrep, EntityManagerInterface $entityManager, $id): Response
    {
        $rendezVou = $RVrep->find($id);
        $form = $this->createForm(RendezVousAdminType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvousAdmin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rendezvous/index.html.twig', [
            'rendezVou' => $rendezVou,
            'form' => $form->createView(),
            "rendezvouses" => $RVrep->findall(), 
        ]);
    }

}
