<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\RendezVous;
use App\Form\RatingType;
use App\Form\RendezVousAdminType;
use App\Form\RendezVousType;
use App\Form\StringIdType;
use App\Repository\ConsultationRepository;
use App\Repository\RendezVousRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// #[Route('/rendez/vous')]
class RendezVousController extends AbstractController
{
    #[Route('/rendez/vous/', name: 'app_rendez_vous_index')]
    public function index(RendezVousRepository $rendezVousRepository, Request $request, EntityManagerInterface $entityManager, ConsultationRepository $Conrep): Response
    {
        // $cons = new Consultation();
        $form = $this->createForm(RatingType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData(); // Get submitted data as an array

            // Access individual form data attributes
            $idrv = $formData['idrv'];
            $rating = $formData['rating'];
            $cons = $Conrep->findOneBy(['rendezvous' => $rendezVousRepository->find($idrv)]);
            $cons->setAvisPatient($rating);
            $entityManager->persist($cons);
            $entityManager->flush();
            return $this->redirectToRoute('app_rendez_vous_index');
        }
        return $this->render('frontClient/viewRendezVous.html.twig', [
            'form' => $form->createView(),
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

    #[Route('/rendez/vous/consultation/{id}/{rating}', name: 'app_rendez_vous_form')]
    public function form(RendezVousRepository $rendezVousRepository, Request $request, EntityManagerInterface $entityManager, ConsultationRepository $Conrep, $id, $rating): Response
    {
        $consultation = $Conrep->find($id);
        $consultation->setAvisPatient($rating);
        $entityManager->persist($consultation);
        $entityManager->flush();
        return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/rendez/vous/new', name: 'app_rendez_vous_new')]
    public function new(UserRepository $Urep, Request $request, EntityManagerInterface $entityManager
    ): Response
    {
        $rendezVou = new RendezVous();
        $serviceName = $searchTerm = $request->query->get('type');
        if($serviceName != null)
            $rendezVou->setNomService($serviceName);
        $rendezVou->setDateR(new \DateTime());
        // $form = $this->createForm(RendezVousType::class, $rendezVou, ['patient' => $Urep->find($idp)]);
        $form = $this->createForm(RendezVousType::class, $rendezVou, ['patient' => $this->getUser()]);
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

    #[Route('/rendez/vous/psy', name: 'app_rendez_vous_confirm')]
    public function psyConfirm(UserRepository $userRepository, RendezVousRepository $rendezVousRepository, Request $request, EntityManagerInterface $entityManager, ConsultationRepository $Conrep): Response
    {
        $consultationId = $request->request->get('consultationId');
        return $this->render('rendez_vous/psyRVConfirmation.html.twig', [
            'consultations' => $Conrep->findAll(),
            'consultationId' => $consultationId,
            "rendezvous" => $rendezVousRepository->findAll(),
            'title' => 'RendezVous',
            'titlepage' => 'RendezVous',
            'controller_name' => 'RendezVousController',
            'service' => 1,
            'part' => 69,
            'rendez_vouses' => $rendezVousRepository->findAll(),
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
        $form = $this->createForm(RendezVousAdminType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontClient/viewRendezVous.html.twig', [
            'rendezVou' => $rendezVou,
            'form' => $form->createView(),
            "rendezvous" => $RVrep->findall(),
            'title' => 'RendezVous',
            'titlepage' => 'RendezVous',
            'controller_name' => 'RendezVousController',
            'service' => 1,
            'part' => 69,
        ]);
    }

    #[Route('/rendez/vous/{id}/delete', name: 'app_rendez_vous_delete')]
    public function delete(Request $request, RendezVousRepository $rendezVou, EntityManagerInterface $entityManager, $id): Response
    {
        $entityManager->remove($rendezVou->find($id));
        $entityManager->flush();

        return $this->redirectToRoute('app_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
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

        return $this->redirectToRoute('app_rendez_vous_confirm', ['psyid' => $rendezVou->getUser()->getId()]);
    }

    #[Route('/admin/rendez/vous', name: 'app_rendezvousAdmin')]
    public function indexAdmin(RendezVousRepository $RVrep, ConsultationRepository $Crep, Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'firstname');
        $sortOrder = $request->query->get('order', 'asc');
        $perPage = 2;

        $currentPage = (int) $request->query->get('page', 1);
        $rvs = $RVrep->search($searchTerm, $sortField, $sortOrder, $currentPage, $perPage);
        $totalArticles = count($rvs);
        $totalPages = ceil($totalArticles / $perPage);
        

        return $this->render('admin/rendezvous/index.html.twig', [
            "rendezvouses" => $rvs,
            "consultations"=> $Crep->findAll(),
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/admin/rendez/vous/{id}/edit', name: 'app_rendez_vous_edit_admin', methods: ['GET', 'POST'])]
    public function editAdmin(Request $request, ConsultationRepository $Crep, RendezVousRepository $RVrep, EntityManagerInterface $entityManager, $id): Response
    {
        $rendezVou = $RVrep->find($id);
        $status = $rendezVou->isStatut();
        $form = $this->createForm(RendezVousAdminType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($rendezVou->isStatut() == true && $status == false) {
                $consultation = new Consultation();
                $consultation->setPatient($rendezVou->getPatient());
                $consultation->setPsychiatre($rendezVou->getUser());
                $consultation->setRendezvous($rendezVou);   
                $consultation->setDuree(new DateTime());
                $consultation->setNote('');
                $consultation->setRecommandationSuivi(false);
                $entityManager->persist($consultation);
            }
            if($rendezVou->isStatut() == false && $status == true) {
                $consultation = new Consultation();
                $entityManager->remove($consultation);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvousAdmin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/rendezvous/index.html.twig', [
            'rendezVou' => $rendezVou,
            'form' => $form->createView(),
            "rendezvouses" => $RVrep->findall(),
            "consultations"=> $Crep->findAll(),
        ]);
    }

}
