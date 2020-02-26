<?php

namespace App\Controller\admin;

use App\Entity\Promotion;
use App\Form\PromotionType;
use App\Repository\PromotionRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends AbstractController
{

    //FONCTION POUR AFFICHER PROMOTIONS
    /**
     * @Route("/admin/promotion/", name="admin_promotions")
     */
    public function ListePromotions(PromotionRepository $repo){

        $promotions = $repo->findAll();

        return $this->render('admin/promotions/promotions.html.twig', [
            'controller_name' => 'AdminController',
            'promotions'=>$promotions
        ]);
    }


    //FONCTION POUR CREER PROMOTION
    /**
     * @Route("/admin/promotions/create", name="admin_promotion_create")
     */
    public function createPromotion(Request $request, ObjectManager $manager){

        $promotion = new Promotion();

        $form = $this->createForm(PromotionType::class,$promotion);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($promotion);
            $manager->flush();
            return $this->redirectToRoute('admin_promotions');
        }
        return $this->render('admin/promotions/promotionCreate.html.twig',[
            'formPromotion'=>$form->createView(),
            'promotion' => $promotion
        ]);
    }

    //FONCTION POUR MODIFIER PROMOTION PAR ID
    /**
     * @Route("/admin/pormotion/{id}/edit", name="admin_promotion_edit")
     */
    public function editEnseignant(Promotion $promotion, Request $request, ObjectManager $manager){

        $form = $this->createForm(PromotionType::class,$promotion);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($promotion);
            $manager->flush();
            return $this->redirectToRoute('admin_promotions');
        }
        return $this->render('admin/promotions/promotionEdit.html.twig',[
            'formPromotion'=>$form->createView(),
            'promotion' => $promotion
        ]);
    }

    //FONCTION POUR SUPPRIMER PROMOTION
    // PAS ACTIVER SUR LE SITE, LA SUPRESSION ENTRAINE LA SUPPRESSION DES ETUDIANTS, ENSEIGNANTS, MATIERES...
    /**
     * @Route("/admin/promotion/{id}/delete", name="admin_promotion_delete")
     */
    public function deleteEnseignant(Promotion $promotion, ObjectManager $manager){

        $manager->remove($promotion);
        $manager->flush();
        return $this->redirectToRoute('admin_promotions');
    }

}
