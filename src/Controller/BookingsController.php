<?php

namespace App\Controller;

use App\Entity\Bookings;
use App\Entity\Cars;
use App\Entity\Plugs;
use App\Form\BookingFormType;
use DateInterval;
use DateTime;
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
        $jsonData = [];
        foreach($this->getUser()->getBookings() as $booking) {
            $station = $booking->getPlug()->getStation();
            $jsonObj = new stdClass();
            $jsonObj->booking_id = $booking->getId();
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
        $error = null;

        $booking = new Bookings();
        $close_window = false;
        $availableCars = [];
        $ownedCars = [];
        foreach($this->getUser()->getCars() as $car) {
            $ownedCars[$car->getLicensePlate()] = $car->getPlugType();
            if(! $car->getBooking())
                $availableCars[$car->getLicensePlate()] = $car->getLicensePlate();
        }
        $form = $this->createForm(BookingFormType::class, options: ['ownedCars' => $availableCars, 'plugId' => (int) $request->query->get('plug')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plug = $doctrine->getRepository(Plugs::class)->findOneBy(['plugId' => $form->get('plug')->getData()]);
            $car = $doctrine->getRepository(Cars::class)->findOneBy(['licensePlate' => $form->get('car')->getData()]);
            $currentTimeDate = new DateTime();

            // verifications
            if(! $plug)
                $error = "The specified plug does not exist!";
            elseif(! $plug->getStatus())
                $error = "The specified plug is unavailable!";
            elseif((! $car) || (! $car->getUsers()->contains($this->getUser())))
                $error = "The chosen car was not added into the 'My Cars' list!\nIf you would like to use this car, please add it to your account first.";
            elseif($form->get('start_time')->getData() < $currentTimeDate)
                $error = "The start time and date cannot be older than the current time and date!";
            elseif($form->get('duration')->getData() < 1 || $form->get('duration')->getData() > 10080)
                $error = "The booking duration must be at least 1 minute and at most 1 week!";
            else {
                $booking->setCar($car);
                $booking->setPlug($plug);
                $booking->setUser($this->getUser());
                $booking->setStartTime($form->get('start_time')->getData());
                $booking->setDuration($form->get('duration')->getData());
                $booking->setEndTime(clone $booking->getStartTime())->getEndTime()->add(new DateInterval('PT' . $booking->getDuration() . 'M'));
            }



            if(! $error) {
                $doctrine->getManager()->persist($booking);
                $doctrine->getManager()->flush();
                $close_window = true;
            }
        }

        // render webpage and send list of table rows to twig
        return $this->render('bookings/booking_creator.html.twig', [
            'submit_error' => $error,
            'close_window' => $close_window,
            'owned_cars' => $ownedCars,
            'createForm' => $form->createView()

        ]);
    }

    #[Route('/booking/{id}/delete', name: 'app_booking_delete')]
    public function booking_delete(string $id, Request $request, ManagerRegistry $doctrine) : Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // <- require user to log in

        $entityManager = $doctrine->getManager();
        $booking = $entityManager->getRepository(Bookings::class)->findOneBy(['id' => $id]);

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
