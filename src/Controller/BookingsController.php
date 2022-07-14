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
                $error = "The chosen car was not added into the 'My Cars' list!";
            elseif($form->get('start_time')->getData() < $currentTimeDate)
                $error = "Start time is older than current date and time!";
            elseif($form->get('duration')->getData() < 1 || $form->get('duration')->getData() > 10080)
                $error = "The duration must be between 1 minute and 1 week!";
            else {
                $booking->setCar($car);
                $booking->setPlug($plug);
                $booking->setUser($this->getUser());
                $booking->setStartTime($form->get('start_time')->getData());
                $booking->setDuration($form->get('duration')->getData());
                $booking->setEndTime(clone $booking->getStartTime())->getEndTime()->add(new DateInterval('PT' . $booking->getDuration() . 'M'));
            }

            // checking if booking is overlapping with another existing one
            $bookingsForPlug = $doctrine->getRepository(Bookings::class)->findBy(['plug' => $plug]);
            foreach ($bookingsForPlug as $book) {
//                if($book->getStartTime()->format("Y-m-d") === $booking->getStartTime()->format("Y-m-d")) {
                if ($booking->getStartTime() <= $book->getStartTime() && $booking->getEndTime() > $book->getStartTime()) {
                    $error = "Booking is overlapping with another that starts at " . $book->getStartTime()->format("H:i");
                    break;
                }
                if ($booking->getStartTime() < $book->getEndTime() && $booking->getEndTime() >= $book->getEndTime()) {
                    $error = "Booking is overlapping with another that ends at " . $book->getEndTime()->format("H:i");
                    break;
                }
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
