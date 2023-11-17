<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifierProfilType;
use App\Repository\ParticipantRepository;
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



    }


