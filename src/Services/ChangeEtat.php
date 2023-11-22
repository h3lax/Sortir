<?php

namespace App\Services;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class ChangeEtat
{


    public function __construct(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager)
    {
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->entityManager = $entityManager;
    }


    //Rechercher les sorties "ouvertes" pour lesquelles la date de clôture est inférieure ou égale à la date du jour (ou nombre max de participants atteints) et les passer en "clôturé"
    public function cloturerSortie(ArrayCollection $sorties, EtatRepository $etatRepository)
    {
        $etat = $etatRepository->findOneBy(['libelle' => 'Clôturée']);

        foreach ($sorties as $sortie)
        {
            //Verifie que la sortie est "ouverte"
            if ($sortie->getEtat()->getLibelle() == 'Ouverte')
            {
                // Vérifie si la date de clôture est dépassée ou si le nombre maximum de participants est atteint
                if ($sortie->getDateHeureDebut() <= new \DateTime() || $sortie->getNbInscriptionsMax() <= count($sortie->getParticipants()))
                {
                $sortie->setEtat($etat);
                $this->entityManager->persist($sortie);
                }
            }

        }
        $this->entityManager->flush();
    }




//Rechercher les sorties "clôturées" pour lesquelles la date de début de sortie est inférieure ou égale à la date/heure courante et les passer en "en cours"



//Rechercher les sorties "en cours" pour lesquelles la date de début + durée est inférieure ou égale à la date/heure courante et les passer en "passé"



//Rechercher les sorties "passées" pour lesquelles la date de début + durée est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"



//Rechercher les sorties "annulées" pour lesquelles la date de début + durée est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"
}