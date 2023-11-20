<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="app_")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/lieux/{id}", name="lieux", methods={"GET"})
     */
    public function lieux($id, VilleRepository $repository): JsonResponse
    {
        $ville = $repository->find($id);
        $lieux = $ville->getLieux();


        return $this->json($lieux, Response::HTTP_OK, [],['groups'=>'GetListeLieux']);
    }

    /**
     * @Route("/coordonnees/{id}", name="coordonnees", methods={"GET"})
     */
    public function coordonnees($id, LieuRepository $repository): JsonResponse
    {
        $lieu = $repository->find($id);

        return $this->json($lieu, Response::HTTP_OK, [],['groups'=>'GetListeLieux']);
    }
}
