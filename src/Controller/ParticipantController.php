<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifierProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function modifierProfil(Request $request): Response
    {
        $participant = $this->getUser();


        $modifierForm = $this->createForm(ModifierProfilType::class, $participant);
        $modifierForm->handleRequest($request);

        if ($modifierForm->isSubmitted() && $modifierForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

                $this->addFlash('success', 'Profil mis à jour avec succès.');

                 return $this->redirectToRoute('accueil');
             }
        return $this->render('participant/modifierProfil.html.twig', [
            'participant' => $participant,
            'modifierForm' => $modifierForm->createView(),
        ]);
        }

    }


