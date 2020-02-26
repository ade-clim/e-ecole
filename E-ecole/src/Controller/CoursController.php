<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CoursController extends AbstractController
{
    //FONCTION DE COURS EN RAPPORT AVEC LE CALENDAR



    //FONCTION POUR AFFICHER COURS DANS CALENDAR
    /**
     * @Route("/enseignant/calendar/cour", name="cours_index", methods={"GET"})
     */
    public function index(CoursRepository $coursRepository): Response
    {
        return $this->render('cours/index.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }

    //FONCTION POUR CREER COUR DANS CALENDAR
    /**
     * @Route("/enseignant/calendar/cour/new", name="cours_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cour = new Cours();
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cour);
            $entityManager->flush();

            return $this->redirectToRoute('cours_index');
        }

        return $this->render('cours/new.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    //FONCTION POUR AFFICHER COURS PAR ID DANS CALENDAR
    /**
     * @Route("/enseignant/calendar/cour/{id}/show", name="cours_show", methods={"GET"})
     */
    public function show(Cours $cour): Response
    {
        return $this->render('cours/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    //FONCTION POUR EDIT COURS DANS CALENDAR
    /**
     * @Route("/enseignant/calendar/cour/{id}/edit", name="cours_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Cours $cour): Response
    {
        $form = $this->createForm(CoursType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cours_index', [
                'id' => $cour->getId(),
            ]);
        }

        return $this->render('cours/edit.html.twig', [
            'cour' => $cour,
            'form' => $form->createView(),
        ]);
    }

    //FONCTION POUR DELETE COURS DANS CALENDAR
    /**
     * @Route("/enseignant/calendar/cour/{id}/delete", name="cours_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Cours $cour): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cours_index');
    }

    //FONCTION POUR AFFICHER ENSEIGNANT PAR CALENDAR
    /**
     * @Route("/enseignant/calendar", name="cour_calendar", methods={"GET"})
     */
    public function calendar(): Response
    {
        $enseignant = $this->get('session')->get('user');
        return $this->render('cours/calendar.html.twig',[
            'test' => $enseignant
        ]);
    }
}
