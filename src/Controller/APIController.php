<?php

namespace App\Controller;

use App\Entity\Stations;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends AbstractController
{
    #[Route('/api/plugs', name: 'api_plugs')]
    public function plugs(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $station = $doctrine->getRepository(Stations::class)->findOneBy(['uuid' => $request->query->get('station') ]);
        if(! $station)
            return new Response("[]", status: 404);

        $jsonData = [];
        foreach($station->getPlugs() as $plug) {
            $jsonObj = new stdClass();
            $jsonObj->id = $plug->getPlugId();
            $jsonObj->status = $plug->getStatus();
            $jsonObj->connector = $plug->getConnectorType();
            $jsonObj->output = $plug->getMaxOutput();
            $jsonData[] = $jsonObj;
        }

        return new Response(json_encode($jsonData, 256));
    }
    #[Route('/api/station/{uuid}', name: 'api_station')]
    public function station(string $uuid, Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $station = $doctrine->getRepository(Stations::class)->findOneBy(['uuid' => $uuid ]);
        if(! $station)
            return new Response("{}", status: 404);

        $jsonObj = new stdClass();
        $jsonObj->uuid = $station->getUuid();
        $jsonObj->name = $station->getName();
        $jsonObj->address = $station->getPlusCode();

        return new Response(json_encode($jsonObj, 256));
    }
}
