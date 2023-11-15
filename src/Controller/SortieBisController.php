<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
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
    public function creer(Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this ->createForm(SortieType::class, $sortie);



        return $this->render('sortie/creer.html.twig', ['sortieForm' => $sortieForm->createView()]);
    }
}

//CHANGER LES DATES DANS LA BDD POUR QUE CE SOIT DATE ET PAS DATETIME