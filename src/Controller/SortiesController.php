<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Sortie;
use App\Form\SearchSortiesType;
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
class SortiesController extends AbstractController
{

    //Accueil : affichage de toutes les sorties existantes, sans aucun filtre pour l'instant

    /**
     * @ROUTE("/", name="accueil")
     */
    public function accueil(
        SortieRepository $sortieRepository,
        Request          $request
    ): Response
    {
        $donnees = new SearchData();
        $filtreSortiesForm = $this->createForm(SearchSortiesType::class, $donnees);
        if (empty($donnees->campus)) {
            $donnees->campus = $this->getUser()->getCampus();
        }
        $filtreSortiesForm->handleRequest($request);
        $sorties = $sortieRepository->rechercheFiltre($donnees);

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
    public function detail(int $id, SortieRepository $sortieRepository): Response
    {

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
        Request                $request,
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository,
        ParticipantRepository  $participantRepository
    ): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortie->setOrganisateur($this->getUser());
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            if ($sortieForm->getClickedButton() && $sortieForm->getClickedButton()->getName() === 'save') {
                $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
                if ($etat) {
                    $sortie->setEtat($etat);
                }
                $entityManager->persist($sortie);
                $entityManager->flush();

            } elseif ($sortieForm->getClickedButton() && $sortieForm->getClickedButton()->getName() === 'push') {
                $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
                if ($etat) {
                    $sortie->setEtat($etat);
                }
                $entityManager->persist($sortie);
                $entityManager->flush();
            }

        }

        return $this->render('sortie/creer.html.twig', ['sortieForm' => $sortieForm->createView()]);
        }


}