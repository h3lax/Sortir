<?php

namespace App\Services;


use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChangeEtat
{


    public function __construct(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager)
    {
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;
        $this->entityManager = $entityManager;
    }

    public function makePass(Array $sorties) {

        $etats = $this->etatRepository->getlibelles();

        $now = new \DateTime();

        foreach ($sorties as $sortie)
        {
            $this->passCloturee($sortie, $etats, $now);
            $this->passEnCours($sortie, $etats, $now);
            $this->passPassee($sortie, $etats, $now);
            $this->passPasseeArchivee($sortie, $etats, $now);
            $this->passAnnuleeArchivee($sortie, $etats, $now);
        }
        $this->entityManager->flush();
    }

    //Rechercher les sorties "ouvertes" pour lesquelles la date de clôture est inférieure ou égale à la date du jour (ou nombre max de participants atteints) et les passer en "clôturé"
    public function passCloturee(Sortie $sortie, Array $etats, \DateTime $now)
    {
            //Verifie que la sortie est "ouverte"
            if ($sortie->getEtat() == $etats['Ouverte'])
            {
                // Vérifie si la date de clôture est dépassée ou si le nombre maximum de participants est atteint
                if ($sortie->getDateLimiteInscription() <= $now || $sortie->getNbInscriptionsMax() <= count($sortie->getParticipants()))
                {
                $sortie->setEtat($etats['Clôturée']);
                $this->entityManager->persist($sortie);
                }
            }
    }


//Rechercher les sorties "clôturées" pour lesquelles la date de début de sortie est inférieure ou égale à la date/heure courante et les passer en "en cours"
    public function passEnCours(Sortie $sortie, Array $etats, \DateTime $now)
    {
            //Verifie que la sortie est "Cloturée"
            if ($sortie->getEtat() == $etats['Clôturée'])
            {
                // Vérifie la condition
                if ($sortie->getDateHeureDebut() <= $now->modify("+{$sortie->getDuree()} minute"))
                {
                    $sortie->setEtat($etats['En cours']);
                    $this->entityManager->persist($sortie);
                }
            }
    }


//Rechercher les sorties "en cours" pour lesquelles la date de début + durée est inférieure ou égale à la date/heure courante et les passer en "passé"
    public function passPassee(Sortie $sortie, Array $etats, \DateTime $now)
    {
            //Verifie que la sortie est "En cours"
            if ($sortie->getEtat() == $etats['En cours'])
            {
                // Vérifie la condition
                if ($sortie->getDateHeureDebut()->modify("+{$sortie->getDuree()} minute") <= $now)
                {
                    $sortie->setEtat($etats['Passée']);
                    $this->entityManager->persist($sortie);
                }
            }
    }


//Rechercher les sorties "passées" pour lesquelles la date de début + durée est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"
    public function passPasseeArchivee(Sortie $sortie, Array $etats, \DateTime $now)
    {

            //Verifie que la sortie est "passée"
            if ($sortie->getEtat() == $etats['Passée'])
            {
                // Vérifie la condition
                if ($sortie->getDateHeureDebut()->modify("+{$sortie->getDuree()} minute + 1 month") <= $now)
                {
                    $sortie->setEtat($etats['Archivée']);
                    $this->entityManager->persist($sortie);
                }
            }
    }

//Rechercher les sorties "annulées" pour lesquelles la date de début + durée est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"
    public function passAnnuleeArchivee(Sortie $sortie, Array $etats, \DateTime $now)
    {

            //Verifie que la sortie est "annulée"
            if ($sortie->getEtat() == $etats['Annulée'])
            {
                // Vérifie la condition
                if ($sortie->getDateHeureDebut()->modify("+{$sortie->getDuree()} minute + 1 month") <= $now)
                {
                    $sortie->setEtat($etats['Archivée']);
                    $this->entityManager->persist($sortie);
                }
            }
    }

}