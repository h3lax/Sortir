<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/sortie", name="sortie_")
 */
class SortieBisController extends AbstractController
{
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
