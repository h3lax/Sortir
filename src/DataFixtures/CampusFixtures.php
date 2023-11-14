<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $nomCampuses=['Nantes','Rennes','Quimper','Niort'];

        foreach ($nomCampuses as $nom){
            $campus = new Campus();
            $campus -> setNom($nom);

            $manager -> persist($campus);
        }

        $manager->flush();
    }

}
