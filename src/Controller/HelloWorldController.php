<?php

namespace App\Controller;

use App\Entity\Cars;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloWorldController extends AbstractController
{
    #[Route("/hello", "hello")]
    public function hello_world(ManagerRegistry $doctrine): Response {
        $car = $doctrine->getRepository(Cars::class)->findOneBy(['licensePlate' => 'AR 77 RAJ']);
        $users = $car->getUsers();
        dd($car);
        return $this->render('HelloWorld.html.twig', [
            'car' => $users[0]->getName()
        ]);
    }
}