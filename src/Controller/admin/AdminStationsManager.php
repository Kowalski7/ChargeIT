<?php

namespace App\Controller\admin;

use App\Entity\Plugs;
use App\Entity\Stations;
use App\Form\StationFormType;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class AdminStationsManager extends AbstractController
{
    #[Route('/admin/stations', name: 'admin_stations')]
    public function stations(ManagerRegistry $doctrine) : Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $stations = $doctrine->getRepository(Stations::class)->findAll();

        // generate JSON for table
        $jsonData = [];
        for($i = 0; $i<sizeof($stations); $i++) {
            $jsonObj = new stdClass();
            $jsonObj->uuid = $stations[$i]->getUuid();
            $jsonObj->name = $stations[$i]->getName();
            $jsonObj->address = $stations[$i]->getPlusCode();
            $jsonData[] = $jsonObj;
        }

        // render webpage and send list of table rows to twig
        return $this->render('admin/stations.html.twig', [
            'jsonData' => $jsonData
        ]);
    }

    #[Route('/admin/station', name: 'admin_station_create')]
    public function station_create(Request $request, ManagerRegistry $doctrine) : Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $station = new Stations();
        $close_window = false;
        $form = $this->createForm(StationFormType::class, $station);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $station->setUuid(Uuid::v4()->toRfc4122());
            $station->setName($form->get('name')->getData());
            $station->setPlusCode($form->get('plusCode')->getData());
            $doctrine->getManager()->persist($station);
            $doctrine->getManager()->flush();
            $close_window = true;
        }

        // render webpage and send list of table rows to twig
        return $this->render('admin/station_creator.html.twig', [
            'createForm'  => $form->createView(),
            'close_window' => $close_window
        ]);
    }

    #[Route('/admin/station/{id}/edit', name: 'admin_station_edit')]
    public function station_edit(string $id, Request $request, ManagerRegistry $doctrine) : Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $station = $doctrine->getRepository(Stations::class)->findOneBy([ 'uuid' => $id ]);
        $plugs = $doctrine->getRepository(Plugs::class)->findBy([ 'station' => $id ]);

        // check if station exists
        if (!$station) {
            return new Response("No station found with ID " . $id, status: 404);
        }

        $form = $this->createForm(StationFormType::class, $station);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $station->setName($form->get('name')->getData());
            $station->setPlusCode($form->get('plusCode')->getData());
            $doctrine->getManager()->flush();
            return $this->redirectToRoute('admin_stations');
        }

        $jsonData = new stdClass();
        $jsonData -> uuid = $station->getUuid();
        $jsonData -> name = $station->getName();
        $jsonData -> address = $station->getPlusCode();

        // generate JSON for table
        $jsonData -> plugs = [];
        for($i = 0; $i<sizeof($plugs); $i++) {
            $jsonObj = new stdClass();
            $jsonObj -> id = $plugs[$i]->getPlugId();
            $jsonObj -> status = $plugs[$i]->getStatus();
            $jsonObj -> connector = $plugs[$i]->getConnectorType();
            $jsonObj -> output = $plugs[$i]->getMaxOutput();
            $jsonData -> plugs[] = $jsonObj;
        }

        // render webpage and send list of table rows to twig
        return $this->render('admin/station_editor.html.twig', [
            'jsonData' => $jsonData,
            'editForm' => $form->createView()
        ]);
    }

    #[Route('/admin/station/{id}/delete', name: 'admin_station_delete')]
    public function station_delete(string $id, Request $request, ManagerRegistry $doctrine) : Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $entityManager = $doctrine->getManager();
        $station = $entityManager->getRepository(Stations::class)->findOneBy([ 'uuid' => $id ]);

        // check if station exists
        if (!$station) {
            return new Response("No station found with ID " . $id, status: 404);
        }

        // delete all plugs associated with the station
        $plugs = $entityManager->getRepository(Plugs::class)->findBy([ 'station' => $station->getUuid() ]);
        for($i = 0; $i<sizeof($plugs); $i++) {
            $entityManager->remove($plugs[$i]);
        }

        // remove station and sync changes
        $entityManager->remove($station);
        $entityManager->flush();
        return $this->redirectToRoute('admin_stations');
    }
}