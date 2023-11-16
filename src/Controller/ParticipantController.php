<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifierProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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

    //Affichage du profil utilisateur
    /**
     * @Route("/participant/modifierprofil", name="participant_modifierprofil")
     */
    public function modifierProfil(Request $request, UserPasswordHasherInterface $mdpHasher): Response
    {
        $modifierForm = $this->createForm(ModifierProfilType::class, $this->getUser());
        $modifierForm->handleRequest($request);

        if ($modifierForm->isSubmitted() && $modifierForm->isValid()) {
            $participant = $modifierForm->getData();
            $motPasse = $modifierForm->get('motPasse')->getData();

            //hashe le MDP
            if (null !== $motPasse){
            $participant->setMotPasse(
                $mdpHasher->hashPassword( $participant, $motPasse ) );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager ->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

                 //return $this->redirectToRoute('participant_modifierprofil');
             }
        return $this->render('participant/modifierProfil.html.twig', [
            'modifierForm' => $modifierForm->createView(),
        ]);
        }

    }


