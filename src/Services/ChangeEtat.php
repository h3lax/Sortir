<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ChangeEtat
{
//Rechercher les sorties "passées" pour lesquelles la date de début + durée est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"
    public function Archive(SortieRepository $sortieRepository, EntityManager $entityManager, Request $request){

        $sortieRepository = $entityManager->getRepository(Sortie::class);

        $sortieArchive = $sortieRepository->findBy(['etat' => '35'])

    }


//Rechercher les sorties "annulées" pour lesquelles la date de début + durée est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"
}