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
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

// #[Route('/rendez/vous')]
class RendezVousController extends AbstractController
{
    #[Route('/rendez/vous/', name: 'app_rendez_vous_index')]
    public function index(RendezVousRepository $rendezVousRepository, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, ConsultationRepository $Conrep): Response
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

    #[Route('/rendez/vous/rating/{idr}', name: 'app_rendez_vous_rating')]
    public function editRating(RendezVousRepository $rendezVousRepository, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, ConsultationRepository $Conrep, $idr): Response
    {
        $email = (new Email())
            ->from('no-reply@nftun.com')
            ->to('abdelbaki.kacem.2023@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        // $cons = new Consultation();
        $form = $this->createForm(RatingType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData(); 
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
            'rendezVou' => $rendezVousRepository->find($idr),
            'idr' => $idr,
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
    public function new(
        UserRepository $Urep,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $rendezVou = new RendezVous();
        $rendezVou->setCertificat(false);
        $serviceName = $searchTerm = $request->query->get('type');
        if ($serviceName != null)
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

            return $this->redirectToRoute('app_rendez_vous_quiz', ['idq' => 0, 'idr' => $rendezVou->getId()], Response::HTTP_SEE_OTHER);
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

        $mail = new PHPMailer(true);

                // Send an email with the reset token
                try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp-relay.brevo.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'fares2business@gmail.com';
                $mail->Password   = 's8qHyjANkQ9DUO7W'; // replace with your password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('fares2business@gmail.com', 'Mailer');
                $mail->addAddress($rendezVou->getPatient()->getEmail(), $rendezVou->getPatient()->getFirstname()); // Add a recipient

                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Appointment confirmation';
                $mail->Body    = '<p>Your appointment has been approuved by : <a href="http://localhost:8000/rendez/vous">view appointment</a></p>';
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                $mail->send();
                $message = 'Message has been sent';
            } catch (Exception $e) {
                $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }


        return $this->redirectToRoute('app_rendez_vous_confirm', ['psyid' => $rendezVou->getUser()->getId()]);
    }

    #[Route('/rendez/vous/{id}/edit/certificat', name: 'app_rendez_vous_edit_certificat')]
    public function editC(Request $request, RendezVousRepository $RVrep, EntityManagerInterface $entityManager, $id): Response
    {
        $rendezVou = $RVrep->find($id);
        $rendezVou->setCertificat(true);
        $entityManager->persist($rendezVou);
        $entityManager->flush();

        return $this->redirectToRoute('app_rendez_vous_confirm', ['psyid' => $rendezVou->getUser()->getId()]);
    }

    #[Route('/admin/rendez/vous', name: 'app_rendezvousAdmin')]
    public function indexAdmin(RendezVousRepository $RVrep, ConsultationRepository $Crep, Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'firstname');
        $sortOrder = $request->query->get('order', 'asc');
        //$perPage = 2; // You can make this a parameter or a constant

        //$currentPage = (int) $request->query->get('page', 1);
        $rvs = $entityManager->getRepository(RendezVous::class)->search($searchTerm, $sortField, $sortOrder);
        $perPage = 4; // Or another suitable default value
        $currentPage = max(1, $request->query->getInt('page', 1));
        $totalrendezvous = count($rvs);
        $totalPages = ceil($totalrendezvous / $perPage);

        // Calculate the slice parameters
        $offset = ($currentPage - 1) * $perPage;
        $length = $perPage;
        // Slice the array to get only the items for the current page
        $rvsForCurrentPage = array_slice($rvs, $offset, $length);

        return $this->render('admin/rendezvous/index.html.twig', [
            "rendezvouses" => $rvsForCurrentPage,
            "consultations" => $Crep->findAll(),
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
            if ($rendezVou->isStatut() == true && $status == false) {
                $consultation = new Consultation();
                $consultation->setPatient($rendezVou->getPatient());
                $consultation->setPsychiatre($rendezVou->getUser());
                $consultation->setRendezvous($rendezVou);
                $consultation->setDuree(new DateTime());
                $consultation->setNote('');
                $consultation->setRecommandationSuivi(false);
                $entityManager->persist($consultation);
            }
            if ($rendezVou->isStatut() == false && $status == true) {
                $consultation = new Consultation();
                $entityManager->remove($consultation);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_rendezvousAdmin', [], Response::HTTP_SEE_OTHER);
        }
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'firstname');
        $sortOrder = $request->query->get('order', 'asc');
        //$perPage = 2; // You can make this a parameter or a constant

        //$currentPage = (int) $request->query->get('page', 1);
        $rvs = $entityManager->getRepository(RendezVous::class)->search($searchTerm, $sortField, $sortOrder);
        $perPage = 4; // Or another suitable default value
        $currentPage = max(1, $request->query->getInt('page', 1));
        $totalrendezvous = count($rvs);
        $totalPages = ceil($totalrendezvous / $perPage);

        // Calculate the slice parameters
        $offset = ($currentPage - 1) * $perPage;
        $length = $perPage;
        // Slice the array to get only the items for the current page
        $rvsForCurrentPage = array_slice($rvs, $offset, $length);

        return $this->render('admin/rendezvous/index.html.twig', [
            'rendezVou' => $rendezVou,
            'form' => $form->createView(),
            "rendezvouses" => $rvsForCurrentPage,
            "consultations" => $Crep->findAll(),
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }

#[Route('/exportpdf/{id}', name: 'app_generer_pdf_historique')]
public function exportPdf(RendezVousRepository $rev, $id): Response
{
    $rend = $rev->find($id);

    // Load your template and data
    $html = $this->renderView('rendez_vous/show.html.twig', ['rv' => $rend]);

    // Create new PDF document
    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Your Title');
    $pdf->SetSubject('Your Subject');

    // Add a page
    $pdf->AddPage();

    // Output the HTML content
    $pdf->writeHTML($html, true, false, true, false, '');

    // Define filename
    $filename = 'certificat.pdf';

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
$pdf->Output($filename, 'D');

    // Ensure that Symfony sends a response object
    return new Response('', Response::HTTP_OK, ['content-type' => 'application/pdf']);
}

}
