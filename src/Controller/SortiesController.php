<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\FiltreSortiesType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortiesController extends AbstractController {

    //Accueil : affichage de toutes les sorties existantes, sans aucun filtre pour l'instant

    /**
     * @ROUTE("/", name="accueil")
     */
    public function accueil(
        SortieRepository $sortieRepository,
        Request $request
    ):Response {
        //initialisation recherche
        $filtreSortiesForm = $this -> createForm(FiltreSortiesType::class);
        $filtreSortiesForm -> handleRequest($request);
        //initialisation du tableau de conditions pour constituer la requete
        $conditions = [];
        //Si on lance un filtre avec le bouton
        if ($filtreSortiesForm->isSubmitted() && $filtreSortiesForm->isValid()) {
            $donnees = $filtreSortiesForm->getData();
            //check de toutes les conditions
            if ($donnees['campus']) {
                $conditions['campus'] =  'siteOrganisateur='.$donnees['campus']->getId();
            }
            if ($donnees['nom']) {
                $conditions['nom'] = 'nom='.$donnees['nom'];
            }
            if ($donnees['debut_periode']) {
                $conditions['debut_periode'] = 'dateHeureDebut>'.$donnees['debut_periode']->format('Y-m-d');
            }
            if ($donnees['fin_periode']) {
                $conditions['fin_periode'] = 'dateHeureDebut<'.$donnees['fin_periode']->format('Y-m-d');
            }
            if ($donnees['organisateur']) {
                $conditions['organisateur'] = 'organisateur='.$this->getUser()->getId();
            }
            if ($donnees['inscrit']) {
                $conditions['inscrit'] = $donnees['organisateur'];
            }
            if ($donnees['pasInscrit']) {
                $conditions['pasInscrit'] = $donnees['organisateur'];
            }
            if ($donnees['past']) {
                $conditions['past'] = $donnees['organisateur'];
            }
        }

        $sorties = $sortieRepository->rechercheFiltre($conditions);
        $currentDate = new \DateTime();

        return $this->render("sortie/accueil.html.twig", [
            "sorties" => $sorties, 
            "currentDate" => $currentDate,
            "filtreSortiesForm" => $filtreSortiesForm->createView()
        ]);
    }

    //Affichage des détails d'une sortie

    /**
     * @ROUTE("/detail/{id}", name="detail")
     */
    public function detail(int $id, SortieRepository $sortieRepository):Response {

        $sortie = $sortieRepository->find($id);
    
        return $this->render("sortie/detail.html.twig", [
            "sortie" => $sortie
        ]
    
    );
    }


    /**
     * @Route("/creer", name="creer")
     */
    public function creer(
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository,
        ParticipantRepository  $participantRepository
    ): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this ->createForm(SortieType::class, $sortie);

        //Juste pour que ça marche, à virer apres
        $etats = $etatRepository->findBy(['libelle' => 'Ouvert']);
        $etat = $etats[0];
        if($etat){
            $sortie -> setEtat($etat);
        }
        $organisateur = $participantRepository->findBy(['id' => 1]);
        $sortie -> setOrganisateur($organisateur[0]);


        $sortieForm -> handleRequest($request);

        if ($sortieForm -> isSubmitted() && $sortieForm ->isValid()){
            $entityManager -> persist($sortie);
            $entityManager ->flush();

        }



        return $this->render('sortie/creer.html.twig', ['sortieForm' => $sortieForm->createView()]);
    }

}