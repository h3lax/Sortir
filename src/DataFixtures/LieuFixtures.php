<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $villeRepository = $manager->getRepository(Ville::class);
        $villes = $villeRepository->findAll();

        for ($i=1 ; $i <= 30; $i++){
            $lieu = new Lieu();
            $lieu -> setNom($faker->word);
            $lieu -> setVille($faker->randomElement($villes));
            $lieu -> setRue($faker->streetAddress);
            $lieu -> setLatitude($faker->latitude);
            $lieu -> setLongitude($faker ->longitude);
            $manager -> persist($lieu);
        }

        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [VilleFixtures::class];
    }
}
