<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StationDetailsController extends AbstractController
{
    #[Route('/station/{id}', name: 'app_station_details')]
    public function index(int $id): Response
    {
        return $this->render('station_details/index.html.twig', [
            'controller_name' => 'StationDetailsController',
            'stid'            => $id
        ]);
    }
}
