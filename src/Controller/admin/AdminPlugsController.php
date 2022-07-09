<?php

namespace App\Controller\admin;

use App\Entity\Plugs;
use App\Entity\Stations;
use App\Form\PlugFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPlugsController extends AbstractController
{
    #[Route('/admin/plug', name: 'admin_plug_create')]
    public function plug_create(Request $request, ManagerRegistry $doctrine) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // <- require user to log in

        // verify if given station exists
        $station_uuid = $request->query->get('station');
        $station = $doctrine->getRepository(Stations::class)->findOneBy(['uuid' => $station_uuid]);
        if(!$station)
            return new Response('<title>ADMIN: ChargeIT Plug Creator</title><body style="text-align: center;"><h1>Invalid or missing station UUID</h1><p>No station could be found with UUID "' . $station_uuid . '"!<br>Please pass a valid station UUID via the \'station\' query paramenter.</p></body>', status:400);

        $plug = new Plugs();
        $close_window = false;
        $form = $this->createForm(PlugFormType::class, $plug);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plug->setStation($station);
            $plug->setConnectorType($form->get('connector_type')->getData());
            $plug->setMaxOutput($form->get('max_output')->getData());
            $plug->setStatus($form->get('status')->getData());
            $doctrine->getManager()->persist($plug);
            $doctrine->getManager()->flush();
            $close_window = true;
        }

        // render webpage and send list of table rows to twig
        return $this->render('admin/plug_creator.html.twig', [
            'createForm' => $form->createView(),
            'close_window' => $close_window
        ]);
    }

    #[Route('/admin/plug/{id}/edit', name: 'admin_plug_edit')]
    public function plug_edit(string $id, Request $request, ManagerRegistry $doctrine) : Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // <- require user to log in

        $plug = $doctrine->getRepository(Plugs::class)->findOneBy(['plugId' => $id]);

        // check if station exists
        if (!$plug) {
            return new Response("No plug found with ID " . $id, status: 404);
        }

        $close_window = false;
        $form = $this->createForm(PlugFormType::class, $plug);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plug->setConnectorType($form->get('connector_type')->getData());
            $plug->setMaxOutput($form->get('max_output')->getData());
            $plug->setStatus($form->get('status')->getData());
            $doctrine->getManager()->flush();
            $close_window = true;
        }

        // render webpage and send list of table rows to twig
        return $this->render('admin/plug_editor.html.twig', [
            'plug_id' => $plug->getPlugId(),
            'editForm' => $form->createView(),
            'close_window' => $close_window
        ]);
    }

    #[Route('/admin/plug/{id}/delete', name: 'admin_plug_delete')]
    public function plug_delete(string $id, Request $request, ManagerRegistry $doctrine) : Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // <- require user to log in

        $entityManager = $doctrine->getManager();
        $plug = $entityManager->getRepository(Plugs::class)->findOneBy(['plugId' => $id]);

        // check if station exists
        if (!$plug) {
            return new Response("No plug found with ID " . $id, status: 404);
        }

        // remove station and sync changes
        $entityManager->remove($plug);
        $entityManager->flush();
        return new Response('<title>ADMIN: Deleting plug...</title><h2>Plug deleted!</h2><p>Please wait...</p><meta http-equiv="refresh" content="1;url=/admin/station/' . $plug->getStation()->getUuid() . '/edit"><script>window.onunload = function() {window.opener.location.reload();}; window.close();</script>');
    }
}