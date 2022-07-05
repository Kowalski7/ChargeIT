<?php

namespace App\Controller;

use App\Entity\Bookings;
use App\Entity\Cars;
use App\Entity\UserCar;
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

        $carRels = $doctrine->getRepository(UserCar::class)->findBy(['user' => $this->getUser()->getId()]);
        $jsonData = [];
        for($i = 0; $i<sizeof($carRels); $i++) {
            $car = $doctrine->getRepository(Cars::class)->findOneBy(['licensePlate' => $carRels[$i]->getCar()]);
            $jsonObj = new stdClass();
            $jsonObj->plate = $car->getLicensePlate();
            $jsonObj->plug = $car->getPlugType();
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
        $carRel = new UserCar();
        $close_window = false;
        $already_added = false;
        $form = $this->createForm(CarFormType::class, $car);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $car->setLicensePlate($form->get('license_plate')->getData());
            $car->setPlugType($form->get('plug_type')->getData());
            $dbCar = $doctrine->getRepository(Cars::class)->findOneBy(['licensePlate' => $car->getLicensePlate()]);
            if(!$dbCar) {
                $doctrine->getManager()->persist($car);
                $carRel->setCar($car);
            } else
                $carRel->setCar($dbCar);
            $carRel->setUser($this->getUser());
            if(! $doctrine->getRepository(UserCar::class)->findOneBy(['user' => $carRel->getUser(), 'car' => $carRel->getCar()])) {
                $doctrine->getManager()->persist($carRel);
                $doctrine->getManager()->flush();
            } else
                $already_added = true;
            $close_window = true;
        }

        // render webpage and send list of table rows to twig
        return $this->render('cars/car_creator.html.twig', [
            'createForm' => $form->createView(),
            'close_window' => $close_window,
            'already_added' => $already_added
        ]);
    }

    #[Route('/car/{plate}/delete', name: 'app_car_delete')]
    public function car_delete(string $plate, string $_route, Request $request, ManagerRegistry $doctrine): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $entityManager = $doctrine->getManager();
        $user_car = $entityManager->getRepository(UserCar::class);

        $car = $entityManager->getRepository(Cars::class)->findOneBy(['licensePlate' => $plate]);
        $carRel = $user_car->findOneBy(['car' => $car, 'user' => $this->getUser()]);

        // check if relations with the car exist
        if (! $carRel) {
            return new Response("You do not have a car with the license plate " . $plate . " added to your account!", status: 400);
        }

        // check if there's an active booking containing the car
        if($entityManager->getRepository(Bookings::class)->findBy(['car' => $car]))
            return new Response('<title>ChargeIT</title><script>alert("The car you are trying to remove is currently part of a reservation!\nThe car can be removed once the reservation expires or is canceled."); window.location.href="' . $this->generateUrl('app_cars') . '";</script>');

        // remove relation between user and car
        $entityManager->remove($carRel);
        $entityManager->flush();

        // check to see if the car is part of other relations or it is safe to remove
        if(! $user_car->findBy(['car' => $plate])) {
            $entityManager->remove($car);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cars');
    }
}