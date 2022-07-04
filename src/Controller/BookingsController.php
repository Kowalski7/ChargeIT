<?php

namespace App\Controller;

use App\Entity\Bookings;
use App\Entity\Cars;
use App\Form\BookingFormType;
use DateInterval;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingsController extends AbstractController
{
    #[Route('/bookings', name: 'app_bookings')]
    public function bookings(Request $request, ManagerRegistry $doctrine): Response
    {
        // render webpage and send list of table rows to twig
        return $this->render('bookings/index.html.twig');
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
        // TODO: query all cars of user
        $form = $this->createForm(BookingFormType::class, $booking, $doctrine->getRepository(Cars::class)->findBy(['u']));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setUser($this->getUser());
            $booking->setCar($form->get('car')->getData());
            $booking->setStartTime($form->get('start_time')->getData());
            $booking->setDuration($form->get('duration')->getData());
            $booking->setEndTime($booking->getStartTime())->getEndTime()->add(new DateInterval('PT' . $booking->getDuration() . 'M'));
            $booking->setPlug($form->get('plug')->getData());
            $doctrine->getManager()->persist($booking);
            $doctrine->getManager()->flush();
            $close_window = true;
        }

        // render webpage and send list of table rows to twig
        return $this->render('main_page/booking_creator.html.twig', [
            'createForm' => $form->createView(),
            'close_window' => $close_window
        ]);
    }
}
