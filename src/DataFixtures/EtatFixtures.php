<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EtatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $etats=['En cours','Fermé','Ouvert','En création'];

        foreach ($etats as $libelle){
            $etat = new Etat();
            $etat -> setLibelle($libelle);

            $manager -> persist($etat);
        }

        $manager->flush();
    }

}
