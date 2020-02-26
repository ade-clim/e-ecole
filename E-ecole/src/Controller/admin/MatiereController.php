<?php

namespace App\Controller\admin;

use App\Entity\Matiere;
use App\Form\MatiereType;
use App\Repository\MatieresRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MatiereController extends AbstractController
{

    //FONCTION POUR AFFICHER MATIERES
    /**
     * @Route("/admin/matieres", name="admin_matieres")
     *
     */
    public function ListeMatieres(Request $request,PaginatorInterface $paginator,MatieresRepository $repo)
    {
        $matiere = $paginator->paginate(
            $repo->findAll(),
            $request->query->getInt('page', 1),
            16
        );

        return $this->render('admin/matieres/matieres.html.twig', [
            'controller_name' => 'AdminController',
            'matiere'=>$matiere
        ]);
    }

    //FONCTION POUR MODIFIER MATIERE PAR ID
    /**
     * @Route("/admin/matiere/{id}/edit", name="admin_matiere_edit")
     */
    public function editMatiere(Matiere $matiere, Request $request, ObjectManager $manager){

        $form = $this->createForm(MatiereType::class,$matiere);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($matiere);
            $manager->flush();
            return $this->redirectToRoute('admin_matieres');
        }
        return $this->render('admin/matieres/matiereEdit.html.twig',[
            'formMatiere'=>$form->createView(),
            'matiere' => $matiere
        ]);
    }

    //FONCTION POUR DELETE MATIERE PAR ID
    /**
     * @Route("/admin/matiere/{id}/delete", name="admin_matiere_delete")
     */
    public function deleteEtudiant(Matiere $matiere, ObjectManager $manager){

        $manager->remove($matiere);
        $manager->flush();
        return $this->redirectToRoute('admin_matieres');
    }



    //FONCTION POUR AFFICHER MATIERE PAR ID
    /**
     * @Route("/admin/matiere/{id}", name="admin_matiere_show")
     */
    public function showMatiere(Matiere $matiere, Request $request){


        return $this->render('admin/matieres/matiereShow.html.twig',[
            'matiere' => $matiere
        ]);
    }


    //FONCTION POUR CREER MATIERE
    /**
     * @Route("/admin/matieres/create", name="admin_matiere_create")
     */
    public function createMatiere(Request $request, ObjectManager $manager){

        $matiere = new Matiere();

        $form = $this->createForm(MatiereType::class,$matiere);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $enseignant = $matiere->getEnseignant();
            $promotion = $matiere->getPromotion();
            $enseignant->addPromotion($promotion);
            $manager->persist($matiere);
            $manager->flush();
            return $this->redirectToRoute('admin_matieres');
        }
        return $this->render('admin/matieres/matiereCreate.html.twig',[
            'formMatiere'=>$form->createView(),
            'matiere' => $matiere
        ]);
    }

}
