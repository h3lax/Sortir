<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangeEtat extends AbstractController
{
//Rechercher les sorties "ouvertes" pour lesquelles la date de clôture est inférieure ou égale à la date du jour (ou nombre max de participants atteints) et les passer en "clôturé"
    public function cloturerSorties (ArrayCollection $sorties, EntityManagerInterface $entityManager,EtatRepository $etatRepository)
    {
        if ()

    }


//Rechercher les sorties "clôturées" pour lesquelles la date de début de sortie est inférieure ou égale à la date/heure courante et les passer en "en cours"



//Rechercher les sorties "en cours" pour lesquelles la date de début + durée est inférieure ou égale à la date/heure courante et les passer en "passé"



//Rechercher les sorties "passées" pour lesquelles la date de début + durée est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"



//Rechercher les sorties "annulées" pour lesquelles la date de début + durée est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"
}