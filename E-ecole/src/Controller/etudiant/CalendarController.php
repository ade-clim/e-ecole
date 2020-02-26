<?php

namespace App\Controller\etudiant;

use App\Entity\Cours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    //FONCTION POUR AFFICHER CALENDAR
    /**
     * @Route("/etudiant/calendar", name="etu_cour_calendar", methods={"GET"})
     */
    public function calendar(): Response
    {

        return $this->render('etudiant/calendar/calendar.html.twig');
    }

    //FONCTION POUR AFFICHER COURS PAR ID DANS CALENDAR
    /**
     * @Route("/etudiant/calendar/cour/{id}/show", name="etu_cours_show", methods={"GET"})
     */
    public function show(Cours $cour): Response
    {
        return $this->render('etudiant/calendar/show.html.twig', [
            'cour' => $cour,
        ]);
    }
}
