<?php

// src/Controller/SubCategorieController.php

namespace App\Controller;

use App\Repository\SousCategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SubCategorieController extends AbstractController
{
    #[Route('/subcategories/{id}', name: 'subcategories')]
    public function index(int $id, SousCategorieRepository $sousCategorieRepository): JsonResponse
    {
        $sousCategories = $sousCategorieRepository->findBy(['Categorie' => $id]);

        $jsonData = array_map(function ($sousCategorie) {
            return [
                'id' => $sousCategorie->getId(),
                'name' => $sousCategorie->getNom(),
            ];
        }, $sousCategories);

        return new JsonResponse($jsonData);
    }
}
