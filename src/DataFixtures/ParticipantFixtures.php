<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParticipantFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $campusRepository = $manager->getRepository(Campus::class);
        $campus = $campusRepository->findAll();

        $admin = new Participant();
        $admin -> setPhotoProfil('photoProfulDefault-655768f80bf61.webp');
        $admin -> setNom('Michel');
        $admin -> setPrenom('Michel');
        $admin -> setTelephone($faker->phoneNumber);
        $admin -> setMail('michel-michel@admin.com');
        $admin -> setPseudo('admin');
        $admin -> setMotPasse('$2y$13$TZ1Pilxq1whZwEmcCfhp2OaYyvMG6NqydEBYmaIc/3AIXxovaf6sW');
        $admin -> setAdministrateur(true);
        $admin -> setCampus($faker->randomElement($campus));
        $admin -> setActif(true);
        $manager -> persist($admin);

        for ($i=1 ; $i <= 80; $i++){
            $participant = new Participant();
            $participant -> setPhotoProfil('photoProfulDefault-655768f80bf61.webp');
            $participant -> setNom($faker->lastName);
            $participant -> setPrenom($faker->lastName);
            $participant -> setTelephone($faker->phoneNumber);
            $participant -> setMail($faker->unique()->email);
            $participant -> setPseudo($faker->unique()->userName);
            $participant -> setMotPasse('$2y$13$TZ1Pilxq1whZwEmcCfhp2OaYyvMG6NqydEBYmaIc/3AIXxovaf6sW');
            $participant -> setAdministrateur(false);
            $participant -> setCampus($faker->randomElement($campus));
            $participant -> setActif(true);
            $manager -> persist($participant);
        }

        $manager->flush();
    }

}
