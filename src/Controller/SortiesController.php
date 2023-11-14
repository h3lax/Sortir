<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortiesController extends AbstractController {

    //Accueil : affichage de toutes les sorties existantes, sans aucun filtre pour l'instant

    /**
     * @ROUTE("/accueil", name="accueil")
     */

    public function accueil(SortieRepository $sortieRepository):Response {

        $sorties = $sortieRepository->findAll();

        $currentDate = new \DateTime();

        return $this->render("accueil.html.twig", [
            "sorties" => $sorties, 
            "currentDate" => $currentDate
        ]);
    }

    //Affichage des détails d'une sortie

    /**
     * @ROUTE("/affichageSorties/{id}", name="Sorties_affichage")
     */
    public function affichage(int $id, SortieRepository $sortieRepository):Response {

        $sortie = $sortieRepository->find($id);
    
        return $this->render("affichageSorties.html.twig", [
            "sortie" => $sortie
        ]
    
    );
    }

    //Affichage des sorties avec filtre par mot-clef

    public function search(Request $request, SortieRepository $sortieRepository):Response {

        $form = $this->createForm(FiltreSortiesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motClef = $form->getData()['motClef'];

    }
    }

}