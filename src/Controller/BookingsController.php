<?php

namespace App\Controller;

use App\Entity\Bookings;
use App\Entity\Cars;
use App\Entity\Plugs;
use App\Entity\Stations;
use App\Entity\UserCar;
use App\Form\BookingFormType;
use DateInterval;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingsController extends AbstractController
{
    #[Route('/bookings', name: 'app_bookings')]
    public function bookings(Request $request, ManagerRegistry $doctrine): Response
    {
        $bookings = $doctrine->getRepository(Bookings::class)->findBy(['user' => $this->getUser()]);
        $stationRepo = $doctrine->getRepository(Stations::class);
        $jsonData = [];
        foreach($bookings as $booking) {
            $station = $stationRepo->findOneBy(['uuid' => $booking->getPlug()->getStation()]);
            $jsonObj = new stdClass();
            $jsonObj->booking_id = $booking->getBookingId();
            $jsonObj->station = $station->getName();
            $jsonObj->plug = $booking->getPlug()->getPlugId() . '. ' . $booking->getPlug()->getConnectorType();
            $jsonObj->car = $booking->getCar()->getLicensePlate();
            $jsonObj->time = $booking->getStartTime()->format('d.M.Y @ G:i e');
            $jsonObj->duration = $booking->getDuration();
            $jsonData[] = $jsonObj;
        }

        // render webpage and send list of table rows to twig
        return $this->render('bookings/index.html.twig', [
            'name' => $this->getUser()->getName(),
            'jsonData' => $jsonData
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/booking', name: 'app_booking_create')]
    public function booking_create(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $booking = new Bookings();
        $close_window = false;
        $ownedCars = [];
        $carRels = $doctrine->getRepository(UserCar::class)->findBy(['user' => $this->getUser()]);
        foreach($carRels as $rel) {
            $ownedCars[$rel->getCar()->getLicensePlate()] = $rel->getCar()->getLicensePlate();
        }
        $form = $this->createForm(BookingFormType::class, options: ['ownedCars' => $ownedCars]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setUser($this->getUser());
            $booking->setCar($doctrine->getRepository(Cars::class)->findOneBy(['licensePlate' => $form->get('car')->getData()]));
            $booking->setStartTime($form->get('start_time')->getData());
            $booking->setDuration($form->get('duration')->getData());
            $booking->setEndTime(clone $booking->getStartTime())->getEndTime()->add(new DateInterval('PT' . $booking->getDuration() . 'M'));
            $booking->setPlug($doctrine->getRepository(Plugs::class)->findOneBy(['plugId' => $form->get('plug')->getData()]));
            $doctrine->getManager()->persist($booking);
            $doctrine->getManager()->flush();
            $close_window = true;
        }

        // render webpage and send list of table rows to twig
        return $this->render('bookings/booking_creator.html.twig', [
            'createForm' => $form->createView(),
            'close_window' => $close_window
        ]);
    }

    #[Route('/booking/{id}/delete', name: 'app_booking_delete')]
    public function booking_delete(string $id, Request $request, ManagerRegistry $doctrine) : Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $entityManager = $doctrine->getManager();
        $booking = $entityManager->getRepository(Bookings::class)->findOneBy(['bookingId' => $id]);

        // check if booking exists
        if (!$booking)
            return new Response("No booking found with ID " . $id, status: 404);

        // check if user has permission to interact with the booking
        if($booking->getUser()->getId() != $this->getUser()->getId())
            return new Response("You do not have permission to alter this booking.", status: 401);

        // remove station and sync changes
        $entityManager->remove($booking);
        $entityManager->flush();
        return $this->redirectToRoute('app_bookings');
    }
}
