<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Categorie;
use App\Form\PublicationType;
use App\Form\CategorieType;
use App\Entity\SousCategorie;
use App\Form\SousCategorieType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Form\EditPublicationType;
use App\Repository\PublicationRepository;
use App\Repository\CommentaireRepository;
use App\Repository\SousCategorieRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BlogController extends AbstractController
{
    #[Route('/blog/{page}', name: 'app_blogClient')]
    public function index(Request $request, PublicationRepository $publicationRepository, int $page,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository): Response
    {
        $itemsPerPage = 4; // Number of items per page
        $totalItems = count($publicationRepository->findAllsortedValide()); // Total number of items
        $totalPages = ceil($totalItems / $itemsPerPage); // Calculate the total number of pages
        $publications = $publicationRepository->findPaginated($page, $itemsPerPage);
        return $this->render('frontClient/blog.html.twig', [
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' => 'Brave Chats',
            'titlepage' => 'Blog - ',
            'publications' => $publications,
            'totalPages' => $totalPages,
            'curentPage'=>$page,
            'commentaireRepository'=>$commentaireRepository,
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'reccpublications' => $publicationRepository->findAllsortedValide(),
        ]);
    }
    #[Route('/blogSousCat/{souscat}/{page}', name: 'app_blogSousCatClient')]
    public function indexparSousCat(Request $request, PublicationRepository $publicationRepository,int $souscat, int $page,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository ): Response
    {
        $itemsPerPage = 4; // Number of items per page
        $totalItems = count($publicationRepository->findbysouscat($souscat)); // Total number of items
        $totalPages = ceil($totalItems / $itemsPerPage); // Calculate the total number of pages
        $publications = $publicationRepository->findPaginatedbysouscat($page, $itemsPerPage,$souscat);
        $Souscat= $sousCategorieRepository->find($souscat);
        return $this->render('frontClient/blogparCat.html.twig', [
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' =>$Souscat->getNom() ,
            'titlepage' => 'Blog - ',
            'publications' => $publications,
            'reccpublications' => $publicationRepository->findAllsortedValide(),
            'totalPages' => $totalPages,
            'curentPage'=>$page,
            'commentaireRepository'=>$commentaireRepository,
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'cat'=>$Souscat->getCategorie(),
        ]);
    }
    #[Route('/blogCat/{cat}/{page}', name: 'app_blogCatClient')]
    public function indexparCat(Request $request, PublicationRepository $publicationRepository,int $cat, int $page,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository  ): Response
    {
        $itemsPerPage = 4; // Number of items per page
        $totalItems = count($publicationRepository->findbycat($cat)); // Total number of items
        $totalPages = ceil($totalItems / $itemsPerPage); // Calculate the total number of pages
        $publications = $publicationRepository->findPaginatedbycat($page, $itemsPerPage,$cat);
        $Cat= $categorieRepository->find($cat);
        return $this->render('frontClient/blogparCat.html.twig', [
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' =>$Cat->getNom() ,
            'titlepage' => 'Blog - ',
            'publications' => $publications,
            'totalPages' => $totalPages,
            'curentPage'=>$page,
            'commentaireRepository'=>$commentaireRepository,
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'reccpublications' => $publicationRepository->findAllsortedValide(),
            'cat'=>$Cat,
        ]);
    }
    #[Route('/blogchooseSousCat/{cat}', name: 'app_choosesousCatClient')]
    public function choosesousCat(Request $request, PublicationRepository $publicationRepository,int $cat,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository  ): Response
    {    
        $Cat= $categorieRepository->find($cat);
        return $this->render('frontClient/choosesouscat.html.twig', [
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' => 'Select Subcategory',
            'titlepage' => 'Blog - ',
            'commentaireRepository'=>$commentaireRepository,
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'reccpublications' => $publicationRepository->findAllsortedValide(),
            'cat'=>$categorieRepository->find($cat),
        ]);
    }
    #[Route('/blogchooseCat', name: 'app_chooseCatClient')]
    public function chooseCat(Request $request, PublicationRepository $publicationRepository,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository  ): Response
    {    
        return $this->render('frontClient/choosecat.html.twig', [
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' => 'Select Category',
            'titlepage' => 'Blog - ',
            'commentaireRepository'=>$commentaireRepository,
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'reccpublications' => $publicationRepository->findAllsortedValide(),
        ]);
    }
    #[Route('/blogDetails/{id}/{showmore}', name: 'app_blogDetails', methods: ['GET', 'POST'])]
    public function show(Request $request, int $showmore, int $id, EntityManagerInterface $entityManager, CommentaireRepository $commentaireRepository, SousCategorieRepository $sousCategorieRepository, CategorieRepository $categorieRepository, PublicationRepository $publicationRepository): Response
    {
        $publication = $publicationRepository->find($id);
        if (!$publication) {
            throw $this->createNotFoundException('No publication found for id '.$id);
        }
        $publication->setVues($publication->getVues()+1);
        $entityManager->flush();
        $Cat=$publication->getCategorie();
        $commentaire = new Commentaire();
        $commentaire->setPublication($publication);
        $formm = $this->createForm(CommentaireType::class, $commentaire);
        $formm->handleRequest($request);
        if ($formm->isSubmitted() && $formm->isValid()) {
            $commentaire->setUser($this->getUser());
            $entityManager->persist($commentaire);
            $entityManager->flush();
            return $this->redirectToRoute('app_blogDetails', ['id'=>$publication->getId(),'showmore'=>$showmore], Response::HTTP_SEE_OTHER);
        }
        return $this->render('frontClient/blog_details.html.twig', [
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' =>$publication->getSousCategorie()->getNom(),
            'titlepage' => 'Blog - ',
            'publication' => $publication,
            'commentaires' => $commentaireRepository->findAllValidatedUnderPublication($publication),
            'souscategoriesundercat'=>$publication->getCategorie()->getSousCategories(),
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'reccpublications' => $publicationRepository->findAllsortedValide(),
            'cat'=>$Cat,
            'commentaireRepository'=>$commentaireRepository,
            'showmore'=>$showmore,
            'commentaire' => $commentaire,
            'form' => $formm->createView(),
        ]);
    }
    #[Route('/blogDetails/{idp}/{showmore}/{idc}', name: 'app_blogDetails_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,int $showmore, int $idc,int $idp, EntityManagerInterface $entityManager,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository,PublicationRepository $publicationRepository): Response
    {
        $publication=$publicationRepository->find($idp);
        $Cat=$publication->getCategorie();
        $newcommentaire = new Commentaire();
        $newcommentaire->setPublication($publication);
        $commentaireupdate=new Commentaire();
        $commentaireupdate= $commentaireRepository->find($idc);
        $formupdate = $this->createForm(CommentaireType::class, $commentaireupdate);
        $formupdate->handleRequest($request);
        if ($formupdate->isSubmitted() && $formupdate->isValid()) {
            $commentaireupdate->setDateM(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('app_blogDetails', ['id'=>$publication->getId(),'showmore'=>$showmore], Response::HTTP_SEE_OTHER);
        }

        return $this->render('frontClient/blog_details.html.twig', [
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' =>$publication->getSousCategorie()->getNom(),
            'titlepage' => 'Blog - ',
            'publication' => $publication,
            'commentaires' => $commentaireRepository->findAllValidatedUnderPublication($publication),
            'souscategoriesundercat'=>$publication->getCategorie()->getSousCategories(),
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'reccpublications' => $publicationRepository->findAllsortedValide(),
            'cat'=>$Cat,
            'commentaireRepository'=>$commentaireRepository,
            'showmore'=>$showmore,
            'commentaire' => $newcommentaire,
            'commentaireupdate' => $commentaireupdate,
            'formupdate' => $formupdate->createView(),
        ]);
    }
    #[Route('/newpub/{cat}/{souscat}', name: 'app_publication_new_blogClient')]
    public function addpub(int $cat,int $souscat,Request $request,SluggerInterface $slugger, ParameterBagInterface $params, EntityManagerInterface $entityManager,PublicationRepository $publicationRepository,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository): Response
    {
        $pub = new Publication();
        $form = $this->createForm(PublicationType::class, $pub);
        $form->handleRequest($request);
        $imageFile = $form->get('imageFile')->getData(); 

        if ($form->isSubmitted() && $form->isValid()) {
            $pub->setUser($this->getUser());
            $pub->setDateC(new \DateTimeImmutable());
            $pub->setValide(0);
            $pub->setCategorie($categorieRepository->find($cat));
            $pub->setSousCategorie($sousCategorieRepository->find($souscat));

            $imageFile = $form->get('imageFile')->getData(); // Ensure 'imageFile' matches your form field name
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $params->get('pub_pictures_directory'), // Make sure this parameter is defined in your services.yaml
                        $newFilename
                    );
                    $pub->setImage($newFilename); // Update the entity with the new filename
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
            }
            else 
            {
                $imageFile="default.png";
                $pub->setImage($imageFile);
            }
            $entityManager->persist($pub);
            $entityManager->flush();

            return $this->redirectToRoute('app_blogCatClient', ['cat'=>$cat,'page'=>1], Response::HTTP_SEE_OTHER);
        }
        return $this->render('frontClient/addpub.html.twig', [
            'controller_name' => 'BlogController',
            'imageUrl' => 'uploads/pub_pictures/' . $pub->getImage(), // Adjust the path as necessary
            'service' => 1,
            'part' => 5,
            'title' => 'Share Your Story',
            'titlepage' => 'Blog - ',
            'commentaireRepository'=>$commentaireRepository,
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'reccpublications' => $publicationRepository->findAllsortedValide(),
            'publication' => $pub,
            'form' => $form->createView(),
            'cat'=>$categorieRepository->find($cat),
            'souscat'=>$sousCategorieRepository->find($souscat),
        ]);
    }
    #[Route('/updatepub/{idp}', name: 'app_publication_update_blogClient')]
    public function updatepub(int $idp,Request $request, EntityManagerInterface $entityManager,PublicationRepository $publicationRepository,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository,SluggerInterface $slugger, ParameterBagInterface $params): Response
    {
        $pub = new Publication();
        $pub=$publicationRepository->find($idp);
        $form = $this->createForm(EditPublicationType::class, $pub);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $pub->setUser($this->getUser());
            //$pub->setValide(0);
            // $pub->setCategorie($categorieRepository->find($pub->getCategorie()->getId()));
            // $pub->setSousCategorie($sousCategorieRepository->find($pub->getSousCategorie()->getId()));
            

            $imageFile = $form->get('imageFile')->getData(); // Ensure 'imageFile' matches your form field name
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $params->get('pub_pictures_directory'), // Make sure this parameter is defined in your services.yaml
                        $newFilename
                    );
                    $pub->setImage($newFilename); // Update the entity with the new filename
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
            }
            $pub->setDateM(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('app_blogDetails', ['id'=>$idp,'showmore'=>0], Response::HTTP_SEE_OTHER);
        }
        return $this->render('frontClient/modifpub.html.twig', [
            'controller_name' => 'BlogController',
            'service' => 1,
            'part' => 5,
            'title' => 'Update Your Post',
            'titlepage' => 'Blog - ',
            'commentaireRepository'=>$commentaireRepository,
            'souscategories'=> $sousCategorieRepository->findAll(),
            'categories'=> $categorieRepository->findAll(),
            'reccpublications' => $publicationRepository->findAllsortedValide(),
            'publication' => $pub,
            'form' => $form->createView(),
            'cat'=>$pub->getCategorie(),
            'souscat'=>$pub->getSousCategorie(),
        ]);
    }


    ////////////////////////////////////////////////ADMIN////////////////////////
    #[Route('/admin/blog', name: 'app_blogAdmin')]
    public function indexAdmin(Request $request, EntityManagerInterface $entityManager,CommentaireRepository $commentaireRepository): Response
    {
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'dateM');
        $sortOrder = $request->query->get('order', 'desc');
        $perPage = 2; // You can make this a parameter or a constant

    $currentPage = (int) $request->query->get('page', 1);

    $publications = $entityManager->getRepository(Publication::class)->search($searchTerm, $sortField, $sortOrder, $currentPage, $perPage);
        //$publications = $publicationRepository->findAllsorteddate();
        $totalPublications = count($publications);
        $totalPages = ceil($totalPublications / $perPage);
    
        return $this->render('admin/blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'publications' => $publications,
             'commentaireRepository'=>$commentaireRepository,
             'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }
    
    #[Route('/admin/blog/cat', name: 'app_blogAdminCat')]
    public function indexAdminCat(Request $request, EntityManagerInterface $entityManager): Response
    {
      
            return $this->render('admin/blog/categories.html.twig', [
                'controller_name' => 'BlogController',
                'categories' => $entityManager->getRepository(Categorie::class)->findAll(),
            ]);
    }
    #[Route('/admin/addcatadmin', name: 'app_cat_add__blogAdmin')]
    public function addcatadmin(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('app_blogAdminCat',[], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/blog/categories.html.twig', [
            'controller_name' => 'BlogController',
            'categories' => $entityManager->getRepository(Categorie::class)->findAll(),
            'formAdd' => $form->createView(),
        ]);
    }
    #[Route('/cat/{idc}', name: 'admin_cat_delete',  methods: ['POST'])]
    public function deletecatadmin(Request $request, int $idc, EntityManagerInterface $entityManager): Response
    { 
        if ($this->isCsrfTokenValid('delete'.$idc, $request->request->get('_token'))) {
            $entityManager->remove($entityManager->getRepository(Categorie::class)->find($idc));
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_blogAdminCat',[], Response::HTTP_SEE_OTHER);
    }
    #[Route('/admin/blog/subcat', name: 'app_blogAdminSubCat')]
    public function indexAdminSubCat(Request $request, EntityManagerInterface $entityManager,): Response
    {
        return $this->render('admin/blog/subcategories.html.twig', [
            'controller_name' => 'BlogController',
            'souscategories' => $entityManager->getRepository(SousCategorie::class)->findAllUnderCategorie($request->query->get('idcat')),
            'idc'=>$request->query->get('idcat'),
        ]);
    }
#[Route('/admin/addsouscatadmin/{idcat}', name: 'app_souscat_add__blogAdmin')]
public function addsouscatadmin(int $idcat, Request $request, EntityManagerInterface $entityManager): Response
{
    $souscategorie = new SousCategorie();
    $categorie = $entityManager->getRepository(Categorie::class)->find($idcat);
    
    if (!$categorie) {
        throw $this->createNotFoundException('No category found for id '.$idcat);
    }

    // Ensure that you have a SousCategorieType form that corresponds to the SousCategorie entity
    $form = $this->createForm(SousCategorieType::class, $souscategorie);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $souscategorie->setCategorie($categorie);
        $entityManager->persist($souscategorie);
        $entityManager->flush();
        return $this->redirectToRoute('app_blogAdminSubCat', ['idcat' => $idcat], Response::HTTP_SEE_OTHER);
    }

    return $this->render('admin/blog/subcategories.html.twig', [
        'controller_name' => 'BlogController',
        'souscategories' => $entityManager->getRepository(SousCategorie::class)->findAllUnderCategorie($idcat),
        'idc' => $idcat,
        'formAdd' => $form->createView(),
    ]);
}
#[Route('/admin/souscat/delete/{id}', name: 'admin_souscat_delete', methods: ['POST'])]
public function deleteSousCat(int $id, Request $request, EntityManagerInterface $entityManager): Response
{
    $sousCategorie = $entityManager->getRepository(SousCategorie::class)->find($id);

    if (!$sousCategorie) {
        throw $this->createNotFoundException('No subcategory found for id '.$id);
    }

    if ($this->isCsrfTokenValid('delete'.$sousCategorie->getId(), $request->request->get('_token'))) {
        $entityManager->remove($sousCategorie);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_blogAdminSubCat', ['idcat' => $sousCategorie->getCategorie()->getId()]);
}


    // #[Route('/cat/{idc}', name: 'admin_cat_delete',  methods: ['POST'])]
    // public function deletecatadmin(Request $request, int $idc, EntityManagerInterface $entityManager): Response
    // { 
    //     if ($this->isCsrfTokenValid('delete'.$idc, $request->request->get('_token'))) {
    //         $entityManager->remove($entityManager->getRepository(Categorie::class)->find($idc));
    //         $entityManager->flush();
    //     }
    //     return $this->redirectToRoute('app_blogAdminCat',[], Response::HTTP_SEE_OTHER);
    // }
    #[Route('/admin/{idp}/coms', name: 'app_blogAdminCom')]
    public function indexAdmincoms(Request $request, PublicationRepository $publicationRepository,int $idp, EntityManagerInterface $entityManager): Response
    {
        $publication = $publicationRepository->find($idp);
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'dateM');
        $sortOrder = $request->query->get('order', 'desc');
        $coms = $entityManager->getRepository(Commentaire::class)->search($searchTerm, $sortField, $sortOrder,$idp);
        return $this->render('admin/blog/coms.html.twig', [
            'pub'=>$publication,
            'controller_name' => 'BlogController',
            'coms' =>$coms,
            'titre'=>$publication->gettitre(),
        ]);
    }
    #[Route('/admin/updatepubadmin/{idp}', name: 'app_publication_update__blogAdmin', methods: ['GET', 'POST'])]
    public function updatepubadmin(int $idp,Request $request, EntityManagerInterface $entityManager,PublicationRepository $publicationRepository,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository,SluggerInterface $slugger, ParameterBagInterface $params): Response
    {
        $pub = new Publication();
        $pub=$publicationRepository->find($idp);
        $formUpdate = $this->createForm(EditPublicationType::class, $pub);
        $formUpdate->handleRequest($request);
        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            // $pub->setUser($this->getUser());
            //$pub->setValide(0);
            // $categorie = $categorieRepository->find($formUpdate->get('Categorie')->getData());
            // $pub->setCategorie($categorie);
            // $souscategorie = $sousCategorieRepository->find($formUpdate->get('SousCategorie')->getData());
            // $pub->setSousCategorie($souscategorie);
            

            $imageFile = $formUpdate->get('imageFile')->getData(); // Ensure 'imageFile' matches your form field name
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $params->get('pub_pictures_directory'), // Make sure this parameter is defined in your services.yaml
                        $newFilename
                    );
                    $pub->setImage($newFilename); // Update the entity with the new filename
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
            }
            $pub->setDateM(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('app_blogAdmin');
        }
        $searchTerm = $request->query->get('search');
        $sortField = $request->query->get('sort', 'dateM');
        $sortOrder = $request->query->get('order', 'desc');
        $perPage = 2; // You can make this a parameter or a constant

    $currentPage = (int) $request->query->get('page', 1);

    $publications = $entityManager->getRepository(Publication::class)->search($searchTerm, $sortField, $sortOrder, $currentPage, $perPage);
        //$publications = $publicationRepository->findAllsorteddate();
        $totalPublications = count($publications);
        $totalPages = ceil($totalPublications / $perPage);
        return $this->render('admin/blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'publications' => $publications,
             'commentaireRepository'=>$commentaireRepository,
             'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'pub'=>$pub,
            'formUpdate' => $formUpdate->createView(),
             'commentaireRepository'=>$commentaireRepository,
        ]);
    }
#[Route('/admin/addpubadmin', name: 'app_publication_add__blogAdmin')]
public function addpubadmin(Request $request, EntityManagerInterface $entityManager,PublicationRepository $publicationRepository,CommentaireRepository $commentaireRepository,SousCategorieRepository $sousCategorieRepository,CategorieRepository $categorieRepository,SluggerInterface $slugger, ParameterBagInterface $params): Response
{
    $pub = new Publication();
    $formAdd = $this->createForm(PublicationType::class, $pub);
    $formAdd->handleRequest($request);
    
    if ($formAdd->isSubmitted() && $formAdd->isValid()) {
        $pub->setUser($this->getUser()); // Set the user (author) of the publication
        $pub->setDateC(new \DateTimeImmutable()); // Set the creation date
        $pub->setValide(1); // Mark the publication as validated

        $imageFile = $formAdd->get('imageFile')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            
            try {
                $imageFile->move(
                    $params->get('pub_pictures_directory'),
                    $newFilename
                );
                $pub->setImage($newFilename);
            } catch (FileException $e) {
                // Handle exception if something happens during file upload
                // Consider setting an error message or logging the error
            }
        } else {
            $pub->setImage("default.png"); // Set a default image if no image is uploaded
        }
        $categorie = $categorieRepository->find($formAdd->get('Categorie')->getData());
        $pub->setCategorie($categorie);
        $souscategorie = $sousCategorieRepository->find($formAdd->get('SousCategorie')->getData());
        $pub->setSousCategorie($souscategorie);
            
        $entityManager->persist($pub);
        $entityManager->flush();

        return $this->redirectToRoute('app_blogAdmin'); // Redirect after successful submission
    }
    $searchTerm = $request->query->get('search');
    $sortField = $request->query->get('sort', 'dateM');
    $sortOrder = $request->query->get('order', 'desc');
    $perPage = 2; // You can make this a parameter or a constant

    $currentPage = (int) $request->query->get('page', 1);

    $publications = $entityManager->getRepository(Publication::class)->search($searchTerm, $sortField, $sortOrder, $currentPage, $perPage);
    //$publications = $publicationRepository->findAllsorteddate();
    $totalPublications = count($publications);
    $totalPages = ceil($totalPublications / $perPage);
    return $this->render('admin/blog/index.html.twig', [
        'controller_name' => 'BlogController',
        'publications' => $publications,
        'commentaireRepository'=>$commentaireRepository,
        'currentPage' => $currentPage,
       'totalPages' => $totalPages,
        'formAdd'=> $formAdd->createView(),
        'commentaireRepository'=>$commentaireRepository,
    ]);
}
    #[Route('/admin/addcomadmin/{idp}', name: 'app_com_add__blogAdmin')]
    public function addcomadmin(int $idp,Request $request, EntityManagerInterface $entityManager,PublicationRepository $publicationRepository,CommentaireRepository $commentaireRepository): Response
    {
        $commentaire = new Commentaire();
        $publication = $entityManager->getRepository(Publication::class)->find($idp);
        $commentaire->setPublication($publication);
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setUser($this->getUser());
            $commentaire->setValide(1);
            $entityManager->persist($commentaire);
            $entityManager->flush();
            return $this->redirectToRoute('app_blogAdminCom', ['idp'=>$idp], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/blog/coms.html.twig', [
            'pub'=>$publication,
            'controller_name' => 'BlogController',
            'formAdd' => $form->createView(),
            'coms' => $commentaire->getpublication()->getCommentaires(),
            'titre'=>$commentaire->getpublication()->gettitre(),
        ]);
    }
    #[Route('/admin/updatecomadmin/{idp}/{idc}', name: 'app_com_update__blogAdmin')]
    public function updatecomadmin(int $idp,int $idc,Request $request, EntityManagerInterface $entityManager,PublicationRepository $publicationRepository,CommentaireRepository $commentaireRepository): Response
    {
        $commentaire =  $commentaireRepository->find($idc);
        $publication = $entityManager->getRepository(Publication::class)->find($idp);
        $commentaire->setPublication($publication);
        $formupdate = $this->createForm(CommentaireType::class, $commentaire);
        $formupdate->handleRequest($request);

        if ($formupdate->isSubmitted() && $formupdate->isValid()) {
            $commentaire->setUser($this->getUser());
            $commentaire->setDateM(new \DateTimeImmutable());
            $commentaire->setValide(1);
            $entityManager->persist($commentaire);
            $entityManager->flush();
            return $this->redirectToRoute('app_blogAdminCom', ['idp'=>$idp], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/blog/coms.html.twig', [
            'pub'=>$publication,
            'controller_name' => 'BlogController',
            'formupdate' => $formupdate->createView(),
            'coms' => $commentaire->getpublication()->getCommentaires(),
            'titre'=>$commentaire->getpublication()->gettitre(),
        ]);
    }
}
