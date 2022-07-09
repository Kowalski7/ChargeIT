<?php

namespace App\Controller;

use App\Entity\Stations;
use stdClass;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $stations = $doctrine->getRepository(Stations::class)->findAll();

        // generate JSON for table
        $jsonData = [];
        for($i = 0; $i<sizeof($stations); $i++) {
            $jsonObj = new stdClass();
            $jsonObj->uuid = $stations[$i]->getUuid();
            $jsonObj->name = $stations[$i]->getName();
            $jsonObj->address = $stations[$i]->getPlusCode();
            $jsonObj->plugs = $stations[$i]->getPlugs();
            $jsonData[] = $jsonObj;
        }

        // render webpage and send list of table rows to twig
        return $this->render('main_page/index.html.twig', [
            'jsonData' => $jsonData
        ]);
    }
}
