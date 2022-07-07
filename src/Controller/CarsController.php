<?php

namespace App\Controller;

use App\Entity\Bookings;
use App\Entity\Cars;
use App\Form\CarFormType;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarsController extends AbstractController
{
    #[Route('/cars', name: 'app_cars')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $cars = $this->getUser()->getCars();
        $jsonData = [];
        for($i=0; $i<sizeof($cars); $i++) {
            $jsonObj = new stdClass();
            $jsonObj->ownIdx = $i;
            $jsonObj->plate = $cars[$i]->getLicensePlate();
            $jsonObj->plug = $cars[$i]->getPlugType();
            $jsonData[] = $jsonObj;
        }
        // render webpage and send list of table rows to twig
        return $this->render('cars/index.html.twig', [
            'jsonData' => $jsonData,
            'name'     => $this->getUser()->getName()
        ]);
    }

    #[Route('/car', name: 'app_car_create')]
    public function car_create(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $car = new Cars();
        $close_window = false;
        $already_added = false;
        $form = $this->createForm(CarFormType::class, $car);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dbCar = $doctrine->getRepository(Cars::class)->findOneBy(['licensePlate' => $form->get('license_plate')->getData()]);
            if(!$dbCar) {
                $car->setLicensePlate($form->get('license_plate')->getData());
                $car->setPlugType($form->get('plug_type')->getData());
                $car->addUser($this->getUser());
                $doctrine->getManager()->persist($car);
            } else {
                if(! $dbCar->getUsers()->contains($this->getUser()))
                    $dbCar->addUser($this->getUser());
                else
                    $already_added = true;
            }

            $doctrine->getManager()->flush();
            $close_window = true;
        }

        // render webpage and send list of table rows to twig
        return $this->render('cars/car_creator.html.twig', [
            'createForm' => $form->createView(),
            'close_window' => $close_window,
            'already_added' => $already_added
        ]);
    }

    #[Route('/car/{ownIdx}/delete', name: 'app_car_delete')]
    public function car_delete(string $ownIdx, string $_route, Request $request, ManagerRegistry $doctrine): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $entityManager = $doctrine->getManager();
        $car = $this->getUser()->getCars()[$ownIdx];

//        $ownership_check = $this->getUser()->getCars()->exists(function($key, $element) use ($plate) {
//            return $element->getLicensePlate() == $plate;
//        });
//
        // check if user owns this car
        if (! $car) {
            return new Response("The car you are trying to remove does not appear to be added to your account!", status: 404);
        }

        // check if there's an active booking containing the car
        if($entityManager->getRepository(Bookings::class)->findBy(['car' => $car]))
            return new Response('<title>ChargeIT</title><script>alert("The car you are trying to remove is currently part of a reservation!\nThe car can be removed once the reservation expires or is canceled."); window.location.href="' . $this->generateUrl('app_cars') . '";</script>');

        // remove relation between user and car
        $this->getUser()->removeCar($car);
        $entityManager->flush();

        // check to see if the car is part of other relations or it is safe to remove
        if(sizeof($car->getUsers()) == 0) {
            $entityManager->remove($car);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_cars');
    }
}