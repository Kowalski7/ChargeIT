<?php

namespace App\Controller\admin;

use App\Entity\Stations;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminLoginController extends AbstractController
{
    #[Route('/admin', name: 'admin_login')]
    public function index(ManagerRegistry $doctrine) : Response {
        // temporary auto-redirect to station editor as there is no admin login page
        header("Location: /admin/stations/");
        return new Response("Redirecting to station editor...", status: 307);
    }
}