<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Etat;
use App\Form\ModifierProfilType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\EtatRepository;
use App\Security\ActifChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ParticipantController extends AbstractController
{

    //Affichage du profil utilisateur
    /**
     * @Route("/participant/profil", name="participant_profil")
     */
    public function profil(ActifChecker $checker): Response
    {
        $participant = $this->getUser();
        $checker->checkPostAuth($participant);

        if (!$participant){
            throw $this->createNotFoundException('Oups... Nous ne parvenons pas à retrouver cet utilisateur.');
        }
        return $this->render('participant/profil.html.twig', ["participant"=>$participant]);
    }

    //Modification du profil utilisateur
    /**
     * @Route("/participant/modifierprofil", name="participant_modifierprofil")
     */
    public function modifierProfil(Request $request, UserPasswordHasherInterface $mdpHasher, SluggerInterface $slugger, EntityManagerInterface $entityManager, ActifChecker $checker): Response
    {

        $participant=$this->getUser();
        $checker->checkPostAuth($participant);

        $modifierForm = $this->createForm(ModifierProfilType::class, $participant);
        $modifierForm->handleRequest($request);

        if ($modifierForm->isSubmitted() && $modifierForm->isValid())
        {
            $participant = $modifierForm->getData();
            $motPasse = $modifierForm->get('motPasse')->getData();

            //PHOTO DE PROFIL
            /** @var UploadedFile $photoProfil */
            $photoProfil = $modifierForm->get('photoProfil')->getData();

            // Si il n'y a pas de photo
            if ($photoProfil)
            {
                $originalFilename = pathinfo($photoProfil->getClientOriginalName(), PATHINFO_FILENAME);
                // Nom du fichier dans l'URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoProfil->guessExtension();

                // déplace la photo dans le dossier
                try
                {
                    $photoProfil->move
                    (
                        $this->getParameter('photo_profil'),
                        $newFilename
                    );
                } catch (FileException $e)
                {

                }

                $participant->setPhotoProfil($newFilename);
            }

            //hashe le MDP
            if (null !== $motPasse)
            {
                $participant->setMotPasse(
                $mdpHasher->hashPassword( $participant, $motPasse ) );
            }

            $entityManager ->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

                 return $this->redirectToRoute('participant_profil');
        }

        $entityManager -> refresh($participant);
        return $this->render('participant/modifierProfil.html.twig', [
            'modifierForm' => $modifierForm->createView(),]);

        }

        //Affichage du profil d'un autre utilisateur

        /**
         * @Route("/participant/profil/{id}", name="profil_autre_utilisateur")
        */
        public function profilAutreUtilisateur(int $id, ParticipantRepository $participantRepository, ActifChecker $checker):Response {
          
            $participant=$this->getUser();
            $checker->checkPostAuth($participant);

            $participant = $participantRepository->find($id);

            return $this->render("participant/profilAutre.html.twig", [
                "participant" => $participant
            ]

        );

        }

        //S'inscrire à une sortie

        /**
         * @Route("/inscription/{id}", name="inscription_sortie")
         */
        public function inscriptionSortie(int $id, EtatRepository $etatRepository, ParticipantRepository $participantRepository, SortieRepository $sortieRepository, Request $request,  EntityManagerInterface $entityManager, ActifChecker $checker): Response {

            $participant=$this->getUser();
            $checker->checkPostAuth($participant);

            $sortie = $sortieRepository->find($id);

            $participant = $this->getUser();

            $currentDate = new \DateTime();

            //Savoir si la sortie existe bien

            if(!$sortie) {
                throw $this->createNotFoundException('Sortie inexistante !');
                return $this->redirectToRoute('sortie_accueil');
            }

            //Savoir si la date de clôture n'a pas été dépassée

            elseif($sortie->getDateLimiteInscription() < $currentDate) {
                $this->addFlash('warning', 'Inscriptions clôturées pour cette sortie !');
               
                $etat = $etatRepository->findOneBy(['libelle' => 'Clôturée']);
                if ($etat) {
                    $sortie->setEtat($etat);
                }

                return $this->redirectToRoute('sortie_accueil');
            }

            //Savoir si le nombre max de participants à la sortie a été atteint

            elseif (count($sortie->getParticipants()) >= $sortie->getNbInscriptionsMax()) {
                $this->addFlash('warning', 'Nombre de participants max atteint pour cette sortie, désolés !');
                return $this->redirectToRoute('sortie_accueil');
            }

            //Savoir si la personne est déjà inscrite ou pas

            elseif ($sortie->estInscrit($participant)) {
                $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie !');
                return $this->redirectToRoute('sortie_accueil');
            }
            
            else {
                $sortie->addParticipant($participant);
                 // Enregistrez les modifications dans la base de données
                 $entityManager->flush();
    
                $this->addFlash('success', 'Vous êtes maintenant inscrit à cette sortie !');

                //Passage de l'état de la sortie à "clôturée" quand le nombre d'inscrits max est atteint
                if(count($sortie->getParticipants()) >= $sortie->getNbInscriptionsMax()) {
                    $etat = $etatRepository->findOneBy(['libelle' => 'Clôturée']);
                    if ($etat) {
                        $sortie->setEtat($etat);
                        $entityManager->persist($sortie);
                        $entityManager->flush();
                    }
                }
            }

            return $this->redirectToRoute('sortie_accueil');

        }
    
        //Se désister d'une sortie

        /**
         * @Route("/desistement/{id}", name="desistement_sortie")
         */
        public function desistementSortie(int $id, EtatRepository $etatRepository, ParticipantRepository $participantRepository, SortieRepository $sortieRepository, Request $request,  EntityManagerInterface $entityManager, ActifChecker $checker): Response {

            $participant=$this->getUser();
            $checker->checkPostAuth($participant);

            $sortie = $sortieRepository->find($id);

            $participant = $this->getUser();

            $currentDate = new \DateTime();

            //Savoir si la sortie existe

            if(!$sortie) {
                throw $this->createNotFoundException('Sortie inexistante !');
                return $this->redirectToRoute('sortie_accueil');
            }

            //Savoir si le participant est bien inscrit à la sortie

            elseif(!$sortie->estInscrit($participant)) {
                $this->addFlash('warning', 'Impossible de se désister : vous ne faites pas partie de cette sortie !');
                return $this->redirectToRoute('sortie_accueil');
            }

            //Savoir si la sortie n'a pas déjà commencé / n'est pas déjà terminée

            elseif($sortie->getDateHeureDebut() < $currentDate) {
                $this->addFlash('warning', 'La sortie a commencé ou est terminée, vous ne pouvez plus vous désinscrire !');
                return $this->redirectToRoute('sortie_accueil');
            }

            else {
                $sortie->removeParticipant($participant);
                // Enregistrez les modifications dans la base de données
                $entityManager->flush();

                $this->addFlash('success', 'Votre inscripton a bien été annulée !');

                //Passage de l'état de la sortie à "créée" quand la date n'est pas dépassée et quand il reste de la place
                if($sortie->getDateHeureDebut() > $currentDate && count($sortie->getParticipants()) < $sortie->getNbInscriptionsMax()) {
                    $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
                    if ($etat) {
                    $sortie->setEtat($etat);
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                }
                }
            }
                return $this->redirectToRoute('sortie_accueil');
            }

        }

    


