<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ModifierProfilType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
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
    public function profil(): Response
    {
        $participant = $this->getUser();

        if (!$participant){
            throw $this->createNotFoundException('Oups... Nous ne parvenons pas à retrouver cet utilisateur.');
        }
        return $this->render('participant/profil.html.twig', ["participant"=>$participant]);
    }

    //Modification du profil utilisateur
    /**
     * @Route("/participant/modifierprofil", name="participant_modifierprofil")
     */
    public function modifierProfil(Request $request, UserPasswordHasherInterface $mdpHasher, SluggerInterface $slugger): Response
    {
        $modifierForm = $this->createForm(ModifierProfilType::class, $this->getUser());
        $modifierForm->handleRequest($request);

        if ($modifierForm->isSubmitted() && $modifierForm->isValid()) {
            $participant = $modifierForm->getData();
            $motPasse = $modifierForm->get('motPasse')->getData();

            //PHOTO DE PROFIL
            /** @var UploadedFile $photoProfil */
            $photoProfil = $modifierForm->get('photoProfil')->getData();

            // Si il n'y a pas de photo
            if ($photoProfil) {
                $originalFilename = pathinfo($photoProfil->getClientOriginalName(), PATHINFO_FILENAME);
                // Nom du fichier dans l'URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoProfil->guessExtension();

                // déplace la photo dans le dossier
                try {
                    $photoProfil->move(
                        $this->getParameter('photo_profil'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... Si probleme... todo
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $participant->setPhotoProfil($newFilename);
            }

            //hashe le MDP
            if (null !== $motPasse){
            $participant->setMotPasse(
                $mdpHasher->hashPassword( $participant, $motPasse ) );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager ->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

                 return $this->redirectToRoute('participant_modifierprofil');
             }
        return $this->render('participant/modifierProfil.html.twig', [
            'modifierForm' => $modifierForm->createView(),
        ]);
        }

        //S'inscrire à une sortie

        /**
         * @Route("/inscription/{id}", name="inscription_sortie")
         */
        public function inscriptionSortie(int $id, ParticipantRepository $participantRepository, SortieRepository $sortieRepository, Request $request,  EntityManagerInterface $entityManager ): Response {

            $sortie = $sortieRepository->find($id);

            if(!$sortie) {
                throw $this->createNotFoundException('Sortie inexistante !');
            }
            else {

                $participant = $this->getUser();

                //Test si sortie n'est pas passée
    
                if ($sortie->estInscrit($participant)) {
                    $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
                }
                else {
                    $sortie->addParticipant($participant);
                }
    
                // Enregistrez les modifications dans la base de données
                $entityManager->flush();
    
                $this->addFlash('success', 'Inscription réussie à la sortie !');
    
                return $this->redirectToRoute('sortie_accueil');
            }
           
        }

        //Se désister d'une sortie

        /**
         * @Route("/desistement/{id}", name="desistement_sortie")
         */
        public function desistementSortie(int $id, ParticipantRepository $participantRepository, SortieRepository $sortieRepository, Request $request,  EntityManagerInterface $entityManager ): Response {
            
            $sortie = $sortieRepository->find($id);
            
            if(!$sortie) { 
                throw $this->createNotFoundException('Sortie inexistante !');
            }
            else {
                $participant = $this->getUser();

            //Test si sortie n'est pas passée

                if ($sortie->estInscrit($participant)) {
                    $sortie->removeParticipant($participant);
                }
                else {
                    $this->addFlash('warning', 'Impossible de se désister : vous ne faites pas partie de cette sortie !');
                }

                // Enregistrez les modifications dans la base de données
                $entityManager->flush();

                $this->addFlash('success', 'Votre inscripton a bien été annulée !');

                return $this->redirectToRoute('sortie_accueil');
            }
            
        }

    }


