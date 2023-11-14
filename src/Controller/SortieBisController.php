<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/sortie", name="sortie_")
 */
class SortieBisController extends AbstractController
{
    /**
     * @Route("/creer", name="creer")
     */
    public function creer(): Response
    {
        return $this->render();
    }
}
