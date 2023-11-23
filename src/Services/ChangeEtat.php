<?php

namespace App\Services;


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


    //Rechercher les sorties "ouvertes" pour lesquelles la date de clôture est inférieure ou égale à la date du jour (ou nombre max de participants atteints) et les passer en "clôturé"
    public function passCloturee(Array $sorties)
    {
        $etats = $this->etatRepository->getlibelles();

        foreach ($sorties as $sortie)
        {
            //Verifie que la sortie est "ouverte"
            if ($sortie->getEtat() == $etats['Ouverte'])
            {
                // Vérifie si la date de clôture est dépassée ou si le nombre maximum de participants est atteint
                if ($sortie->getDateLimiteInscription() <= new \DateTime() || $sortie->getNbInscriptionsMax() <= count($sortie->getParticipants()))
                {
                $sortie->setEtat($etats['Clôturée']);
                $this->entityManager->persist($sortie);
                }
            }

        }
        $this->entityManager->flush();
    }


//Rechercher les sorties "clôturées" pour lesquelles la date de début de sortie est inférieure ou égale à la date/heure courante et les passer en "en cours"
    public function passEnCours(Array $sorties)
    {
        $etats = $this->etatRepository->getlibelles();

        foreach ($sorties as $sortie)
        {
            //Verifie que la sortie est "Cloturée"
            if ($sortie->getEtat() == $etats['Clôturée'])
            {
                // Vérifie la condition
                if ($sortie->getDateHeureDebut() <= new \DateTime())
                {
                    $sortie->setEtat($etats['En cours']);
                    $this->entityManager->persist($sortie);
                }
            }

        }
        $this->entityManager->flush();
    }


//Rechercher les sorties "en cours" pour lesquelles la date de début + durée est inférieure ou égale à la date/heure courante et les passer en "passé"
    public function passPassee(Array $sorties)
    {
        $etats = $this->etatRepository->getlibelles();

        foreach ($sorties as $sortie)
        {
            //Verifie que la sortie est "En cours"
            if ($sortie->getEtat() == $etats['En cours'])
            {
                // Vérifie la condition
                if ($sortie->getDateHeureDebut()<= new \DateTime())
                {
                    $sortie->setEtat($etats['Passée']);
                    $this->entityManager->persist($sortie);
                }
            }

        }
        $this->entityManager->flush();
    }


//Rechercher les sorties "passées" pour lesquelles la date de début à la date/heure courante plus 1 mois et les passer en "historisée"
    public function passPasseeArchivee(Array $sorties)
    {
        $etats = $this->etatRepository->getlibelles();

        $dateArchivage=(new \DateTime())->modify('+1 month');

        foreach ($sorties as $sortie)
        {
            //Verifie que la sortie est "ouverte"
            if ($sortie->getEtat() == $etats['Passée'])
            {
                // Vérifie la condition
                if ($sortie->getDateHeureDebut() <= $dateArchivage)
                {
                    $sortie->setEtat($etats['Archivée']);
                    $this->entityManager->persist($sortie);
                }
            }

        }
        $this->entityManager->flush();
    }

//Rechercher les sorties "annulées" pour lesquelles la date de début est inférieure à la date/heure courante plus 1 mois et les passer en "historisée"
    public function passAnnuleeArchivee(Array $sorties)
    {
        $etats = $this->etatRepository->getlibelles();

        $dateArchivage=(new \DateTime())->modify('+1 month');

        foreach ($sorties as $sortie)
        {
            //Verifie que la sortie est "ouverte"
            if ($sortie->getEtat() == $etats['Annulée'])
            {
                // Vérifie la condition
                if ($sortie->getDateHeureDebut() + $sortie->getDuree() <= $dateArchivage)
                {
                    $sortie->setEtat($etats['Archivée']);
                    $this->entityManager->persist($sortie);
                }
            }

        }
        $this->entityManager->flush();
    }

}