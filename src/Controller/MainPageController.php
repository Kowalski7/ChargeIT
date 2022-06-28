<?php

namespace App\Controller;

use App\Entity\Plugs;
use App\Entity\Stations;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $objList = [];
        $stations = $doctrine->getRepository(Stations::class)->findAll();
        $plugRepo = $doctrine->getRepository(Plugs::class);

        // generate table rows and add them to a list of objects for twig
        for($i = 0; $i<sizeof($stations); $i++) {
            $finalObj = "<td>". $stations[$i]->getName() . "</td><td>" . $stations[$i]->getPlusCode() . "</td>";
            $typesObj = "<td>";
            $powersObj = "<td>";
            $plugs = $plugRepo->findBy(['station' => $stations[$i]->getPlusCode() ]);
            for($j = 0; $j < sizeof($plugs); $j++) {
                if($plugs[$j]->getStatus()) {
                    $typesObj = $typesObj . $plugs[$j]->getConnectorType() . '<br>';
                    $powersObj = $powersObj . $plugs[$j]->getMaxOutput() . '<br>';
                }
            }
            $finalObj = $finalObj . $typesObj . "</td>" . $powersObj . "</td>";
            $objList[] = $finalObj;
        }

        // render webpage and send list of table rows to twig
        return $this->render('main_page/index.html.twig', [
            'objects' => $objList
        ]);
    }


}
