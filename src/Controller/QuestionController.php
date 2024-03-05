<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\QuestionType;
use App\Form\ReponseType;
use App\Repository\QuestionRepository;
use App\Repository\FormulaireRepository;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response as HttpResponse; // To avoid class name conflict

#[Route('/question')]
class QuestionController extends AbstractController
{ private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/admin/quiz', name: 'app_question_index')]
    public function index(QuestionRepository $questionRepository, Request $request, EntityManagerInterface $entityManager, FormulaireRepository $formulaireRepository): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'question');
        $sortOrder = $request->query->get('order', 'asc');
        $questions = $this->entityManager->getRepository(Question::class)->search($searchTerm, $sortField, $sortOrder);
        

        if ($form->isSubmitted() && $form->isValid()) {
          
            $entityManager->persist($question);
            $entityManager->flush();
            $id=$question->getId();

            return $this->redirectToRoute('app_question_index', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/quiz/index.html.twig', [
            'questions' => $questions,
            'form' => $form->createView(),
            'formel' => $formulaireRepository->findAll()

            
        ]);
    }

    
    #[Route('/new', name: 'app_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $question = new Question();
       
        
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($question);
            $entityManager->flush();
            $id=$question->getId();

            return $this->redirectToRoute('app_question_show2', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
 
        return $this->render('question/index.html.twig', [
            'questions' => $questionRepository->findAll(),
            'controller_name' => 'questionController',
            'service' => 1,
            'part' => 5,
            'title' => "question",
            'titlepage' => 'question - ', 'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/quiz/{id}', name: 'app_question_show', methods: ['GET', 'POST'])]
    public function show(int $id, QuestionRepository $questionRepository, ReponseRepository $rep, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Fetch the question based on the ID in the route
        $question = $questionRepository->find($id);

        if (!$question) {
            throw $this->createNotFoundException('No question found for id '.$id);
        }
        // Now that $question is defined, use it to find related responses
        $repos = $rep->findBy(['question' => $question]);
        $response = new Reponse();
        $response->setQuestion($question); // Associate the response with the question here

        $form = $this->createForm(ReponseType::class, $response);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($response);
            if (1> sizeof($rep->findBy(['question'=>$questionRepository->find($id)])))
            $question ->setActive(false);
            else
            $question ->setActive(true);
            $entityManager->flush();

            return $this->redirectToRoute('app_question_show', ['id' => $id]);
        }

        // Assuming you have other logic here to prepare for rendering the form

        return $this->render('admin/quiz/reponse.html.twig', [
            'question' => $question,
            'Listrepons' => $repos,
            'controller_name' => 'questionController',
            'form' => $form->createView(), // <-- Make sure to call createView()
            'service' => 1,
            'part' => 5,
            'title' => "question",
            'titlepage' => 'question - ',
        ]);

    }
    #[Route('/{idp}/validation', name: 'app_question_validate', methods: ['GET', 'POST'])]
    public function validate(Request $request, $idp, EntityManagerInterface $entityManager,
    QuestionRepository $questionRepository,ReponseRepository $rep): Response
    {
        $question = new Question();
        $question  = $questionRepository->find($idp);
        if (2 >= sizeof($rep->findBy(['question'=>$questionRepository->find($idp)])))
        $question ->setActive(!($question ->isActive()));
    else
    $question ->setActive($question ->isActive());
        $entityManager->flush();
        return $this->redirectToRoute('app_question_index');
    }
    #[Route('/{id}', name: 'app_question_show2', methods: ['GET', 'POST'])]
    public function show2(int $id, QuestionRepository $questionRepository, ReponseRepository $rep, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Fetch the question based on the ID in the route
        $question = $questionRepository->find($id);

        if (!$question) {
            throw $this->createNotFoundException('No question found for id '.$id);
        }

        // Now that $question is defined, use it to find related responses
        $repos = $rep->findBy(['question' => $question]);


        $response = new Reponse();
        $response->setQuestion($question); // Associate the response with the question here

        $form = $this->createForm(ReponseType::class, $response);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($response);
            $entityManager->flush();

            return $this->redirectToRoute('app_question_show2', ['id' => $id]);
        }

        // Assuming you have other logic here to prepare for rendering the form

        return $this->render('question/show2.html.twig', [
            'question' => $question,
            'Listrepons' => $repos,
            'controller_name' => 'questionController',
            'form' => $form->createView(), // <-- Make sure to call createView()
            'service' => 1,
            'part' => 5,
            'title' => "question",
            'titlepage' => 'question - ',
        ]);

    }


    #[Route('/{id}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, QuestionRepository $questionRepository,FormulaireRepository $formulaireRepository, EntityManagerInterface $entityManager): Response
    {
        $question = $questionRepository->find($id);
         
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();
            $id=$question->getId();

            return $this->redirectToRoute('app_question_index', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
    
        if (!$question) {
            throw $this->createNotFoundException('No question found for id '.$id);
        }
        $formUpdate = $this->createForm(QuestionType::class, $question);
        $formUpdate->handleRequest($request);
    
        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'question updated successfully.');
            return $this->redirectToRoute('app_question_index');
        }
        return $this->render('admin/quiz/index.html.twig', [
             'questions' => $questionRepository->findAll(), 
              'formUpdate' => $formUpdate->createView(),
              'question'=>$question,
              'form' => $form->createView(),
              'formel' => $formulaireRepository->findAll(),
        ]);
        
    }
    

    #[Route('/delete/{id}', name: 'app_question_delete')]
    public function delete(Request $request, int $id, QuestionRepository $questionRepository, EntityManagerInterface $entityManager): Response
    {
        $question = $questionRepository->find($id);
    
        if (!$question) {
            throw $this->createNotFoundException('No question found for id '.$id);
        }

        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('app_question_index', []);
    }
}
