<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Sortie;
use App\Form\SearchSortiesType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Security\ActifChecker;
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
        Request          $request,
        ActifChecker $checker
    ): Response
    {

        $participant=$this->getUser();
        $checker->checkPostAuth($participant);

        $donnees = new SearchData();
        if (empty($donnees->campus)) {
            $donnees->campus = $this->getUser()->getCampus();
        }
        $filtreSortiesForm = $this->createForm(SearchSortiesType::class, $donnees);
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
     * @Route("/detail/{id}", name="detail")
     */
    public function detail(int $id, SortieRepository $sortieRepository, ActifChecker $checker): Response
    {

        $participant=$this->getUser();
        $checker->checkPostAuth($participant);

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
        ParticipantRepository  $participantRepository,
        ActifChecker $checker

        

    ): Response
    {
        $participant=$this->getUser();
        $checker->checkPostAuth($participant);

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
                $this -> addFlash('success','Sortie créée! N\'oubliez pas que vous devrez la publier pour la rendre accessible à d\'autres participants!');
                return $this->redirectToRoute('sortie_accueil');

            } elseif ($sortieForm->getClickedButton() && $sortieForm->getClickedButton()->getName() === 'push') {
                $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
                if ($etat) {
                    $sortie->setEtat($etat);
                }
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this -> addFlash('success','Sortie publiée!');
                return $this->redirectToRoute('sortie_accueil');
            }

        }

        return $this->render('sortie/creer.html.twig', ['sortieForm' => $sortieForm->createView()]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier(
        Request                $request,
        EntityManagerInterface $entityManager,
        EtatRepository         $etatRepository,
        SortieRepository       $sortieRepository,
        $id
    ): Response{

        $sortie = $sortieRepository->find($id);
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortie->setOrganisateur($this->getUser());
        $sortieForm->handleRequest($request);


        return $this->render('sortie/modifier.html.twig', ['sortieForm' => $sortieForm->createView(), 'sortie'=>$sortie]);
    }


}