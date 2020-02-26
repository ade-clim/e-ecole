<?php

namespace App\Controller\enseignant;

use App\Entity\Matiere;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MatiereController extends AbstractController
{
    //FONCTION POUR AFFICHER MATIERE PAR ID
    /**
     * @Route("/enseignant/matiere/{id}", name="ens_matiere_show")
     */
    public function index(Matiere $matiere)
    {
        return $this->render('enseignant/matieres/matieresShow.html.twig', [
            'matiere'=>$matiere,
        ]);
    }
}
