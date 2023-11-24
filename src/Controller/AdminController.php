<?php

namespace App\Controller;

use App\Form\CreerParticipantFormType;
use App\Form\ModifierProfilType;
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

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/creer", name="creer")
     */
    public function creerProfil(Request $request, UserPasswordHasherInterface $mdpHasher, EntityManagerInterface $entityManager, ActifChecker $checker,SluggerInterface $slugger): Response
    {

        $participant=$this->getUser();
        $checker->checkPostAuth($participant);

        $creerForm = $this->createForm(CreerParticipantFormType::class);
        $creerForm->handleRequest($request);

        if ($creerForm->isSubmitted() && $creerForm->isValid())
        {
            $participant = $creerForm->getData();
            $motPasse = $creerForm->get('motPasse')->getData();

            //hashe le MDP
            if (null !== $motPasse)
            {
                $participant->setMotPasse(
                    $mdpHasher->hashPassword( $participant, $motPasse ) );
            }
            $this->addFlash('success', 'Participant créé avec succès.');

        }

        $entityManager ->persist($participant);
        $entityManager->flush();

        return $this->render('admin/creerParticipant.html.twig', [
            'creerForm' => $creerForm->createView(),]);

    }
}
