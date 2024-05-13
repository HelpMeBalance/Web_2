<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formulaire;
use App\Entity\Formulairej;
use App\Form\FormulaireType;
use App\Form\QuizType;
use App\Repository\FormulaireRepository;
use App\Repository\QuestionRepository;
use App\Repository\RendezVousRepository;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

#[Route('/formulaire')]
class FormulaireController extends AbstractController
{
    #[Route('/', name: 'app_formulaire_index', methods: ['GET'])]
    public function index(FormulaireRepository $formulaireRepository,): Response
    {
        return $this->render('formulaire/index.html.twig', [
            'formulaires' => $formulaireRepository->findAll(),
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' => "Formulaire" ,
            'titlepage' => 'Formulaire - ',
        ]);
    }
    #[Route('/index2/{rv}', name: 'app_formulaire_index2', methods: ['GET'])]
    public function index2(FormulaireRepository $formulaireRepository,$rv, RendezVousRepository $RVrep): Response
    {
        return $this->render('formulaire/index2.html.twig', [
            'formulaires' => $formulaireRepository->findAll(),
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' => "Formulaire" ,
            'titlepage' => 'Formulaire - ',
            'rv'=> $RVrep->find($rv) ,
        ]);
    }
    #[Route('/new', name: 'app_formulaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formulaire = new Formulaire();
        $formulairej = new Formulairej();

        
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formulaire);
            $entityManager->flush();
          
            
           



            return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formulaire/new.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_formulaire_show', methods: ['GET'])]
    public function show(Formulaire $formulaire): Response
    {
        return $this->render('formulaire/show.html.twig', [
            'formulaire' => $formulaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formulaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormulaireType::class, $formulaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formulaire/edit.html.twig', [
            'formulaire' => $formulaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_formulaire_delete', methods: ['POST'])]
    public function delete(Request $request, Formulaire $formulaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formulaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formulaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formulaire_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/quiz/{idq}/{idr}', name: 'app_rendez_vous_quiz')]
    public function quiz(RendezVousRepository $rendezVousRepository, Request $request,$idr, EntityManagerInterface $entityManager, QuestionRepository $questionRepository, ReponseRepository $reposeRepository, $idq): Response
    {
        $question = $questionRepository->findAll()[$idq];
        $formulaire = new Formulaire();
        $formulairej = new Formulairej();

        $formulaire->setUser($this->getUser());
        $formulaire->setRendezVous($rendezVousRepository->find($idr));
//        $form = $this->createForm(QuizType::class, $formulaire, ['question' => $question]);
        $rv=$rendezVousRepository->find($idr);
        $form = $this->createForm(FormulaireType::class, $formulaire, ['question' => $question]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formulaire->addQuestion($question);
            $entityManager->persist($formulaire);
            $entityManager->flush();
            $reponses = $formulaire->getReponse();

            // Example: Iterating over the collection
            foreach ($reponses as $reponse) {
                $formulairej->setReponse($reponse->getReponse());
            }

            $formulairej->setIdf($formulaire->getId());
            $question=
            $formulairej->setQuestion($question-> getQuestion());
           
            $formulairej->setIdr($idr); 
            $entityManager->persist($formulairej);
            $entityManager->flush();

            if ($idq >= sizeof($questionRepository->findAll()) - 1)
                {return $this->redirectToRoute('app_formulaire_index2', ['rv'=>$idr]);}
                
            
            else
            return $this->redirectToRoute('app_rendez_vous_quiz', ['idq' => $idq + 1, 'idr'=>$idr], Response::HTTP_SEE_OTHER);
        }

        if($question->isActive() == false){
            if($idq >= sizeof($questionRepository->findAll()) - 1){
                return $this->redirectToRoute('app_formulaire_index2',['rv' => $idr]);
            }
            else{
            return $this->redirectToRoute('app_rendez_vous_quiz', ['idq' => $idq + 1, 'idr'=>$idr], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('frontClient/formulaire.html.twig', [
            'question' => $question,
            "form" => $form->createView(),
            'reponses' => $reposeRepository->findBy(['question'=> $question->getId()]),
            'title' => 'RendezVous',
            'titlepage' => 'RendezVous',
            'controller_name' => 'RendezVousController',
            'service' => 1,
            'part' => 69,
            'rv' => $rv,
        ]);

    }
}
