<?php

namespace App\Controller\admin;

use App\Entity\Enseignant;
use App\Entity\Login;
use App\Entity\MessageRecu;
use App\Entity\Messagerie;
use App\Entity\MessageSend;
use App\Form\EnseignantType;
use App\Form\MessageSendType;
use App\Repository\AdminRepository;
use App\Repository\EnseignantRepository;
use App\Repository\MessagerieRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EnseignantController extends AbstractController
{

    //FONCTION POUR AFFICHER ENSEIGNANT EN VUE CLASSIC
    /**
     * @Route("/admin/enseignants/classic", name="admin_enseignants_view_classic")
     */
    public function ListeEnseignantsClassic(Request $request,PaginatorInterface $paginator,EnseignantRepository $repo)
    {
        $enseignant = $paginator->paginate(
            $repo->findAll(),
            $request->query->getInt('page', 1),
            16
        );

        return $this->render('admin/enseignants/enseignantsViewClassic.html.twig', [
            'controller_name' => 'AdminController',
            'enseignant'=>$enseignant
        ]);
    }

    //FONCTION POUR AFFICHER ENSEIGNANT EN VUE AVANCEE
    /**
     * @Route("/admin/enseignants/avancee", name="admin_enseignants_view_avancee")
     */
    public function ListeEnseignantsAvancee(Request $request,PaginatorInterface $paginator,EnseignantRepository $repo)
    {
        $enseignant = $paginator->paginate(
            $repo->findAll(),
            $request->query->getInt('page', 1),
            16
        );

        return $this->render('admin/enseignants/enseignantsViewAvancee.html.twig', [
            'controller_name' => 'AdminController',
            'enseignant'=>$enseignant
        ]);
    }


    //FONCTION POUR MODIFIER ENSEIGNANT
    /**
     * @Route("/admin/enseignant/{id}/edit", name="admin_enseignant_edit")
     */
    public function editEnseignant(Enseignant $enseignant, Request $request, ObjectManager $manager){

        $form = $this->createForm(EnseignantType::class,$enseignant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($enseignant);
            $manager->flush();
            return $this->redirectToRoute('admin_enseignant_show',array('id'=>$enseignant->getId()));
        }
        return $this->render('admin/enseignants/enseignantEdit.html.twig',[
            'formEnseignant'=>$form->createView(),
            'enseignant' => $enseignant
        ]);
    }

    //FONCTION POUR DELETE ENSEIGNANT
    /**
     * @Route("/admin/enseignant/{id}/delete", name="admin_enseignant_delete")
     */
    public function deleteEnseignant(Enseignant $enseignant, ObjectManager $manager){

        $manager->remove($enseignant);
        $manager->flush();
        return $this->redirectToRoute('admin_enseignants_view_avancee');
    }


    //FONCTION POUR AFFICHER ENSEIGNANT SELON ID
    /**
     * @Route("/admin/enseignant/{id}", name="admin_enseignant_show")
     */
    public function showEnseignant(Request $request, Enseignant $enseignant, MessagerieRepository $repoMessagerie, ObjectManager $manager, AdminRepository $repoAdmin){

        //recup utilisateur en  session
        $email = $this->getUser()->getEmail();
        $admin = $repoAdmin->findOneByEmail($email);

        //formulaire de recuperation du popup messagerie
        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //enregitrement du message pour expediteur
            $messageSend->setMessagerie($admin->getMessagerie());
            $messageSend->setDate(new \DateTime('now'));

            //on s'occupe maintenant d'enregistrer le message pour le destinataire;
            $messagerieId = $repoMessagerie->findOneByEmail($messageSend->getEmail());
            $messageRecu = new MessageRecu();
            $messageRecu->setEmail($messageSend->getEmail());
            $messageRecu->setTitre($messageSend->getTitre());
            $messageRecu->setBody($messageSend->getBody());
            $messageRecu->setDate($messageSend->getDate());
            $messageRecu->setMessagerie($messagerieId);
            $messageRecu->setOnline(1);


            $manager->persist($messageRecu);
            $manager->persist($messageSend);
            $manager->flush();
            return $this->redirectToRoute('admin_enseignant_show',array('id'=>$enseignant->getId()));
        }
        return $this->render('admin/enseignants/enseignantShow.html.twig',[
            'formMessageSend'=>$form->createView(),
            'enseignant' => $enseignant
        ]);
    }

    //FONCTION POUR CREER ENSEIGNANT
    /**
     * @Route("/admin/enseignants/create", name="admin_enseignant_create")
     */
    public function createEnseignant(Request $request, ObjectManager $manager){

        //faker permet de generer des information aleatoire
        $faker = Factory::create('fr_FR');

        $enseignant = new Enseignant();

        $form = $this->createForm(EnseignantType::class,$enseignant);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $login = new Login();
            $login->setLogin($enseignant->getPrenom().$enseignant->getNom()."@e-ecole.com");
            $login->setPassword($faker->password);
            $login->setEmail($enseignant->getPrenom().".".$enseignant->getNom()."@e-ecole.com");
            $login->setRole('ROLE_ADMIN');

            // creer messagerie pour l'etudiant
            $messagerie = new Messagerie();
            $messagerie->setEmail($login->getEmail());
            $enseignant->setMessagerie($messagerie);

            $enseignant->setLogin($login);
            $enseignant->setEmail($login->getEmail());
            $manager->persist($enseignant);
            $manager->flush();
            return $this->redirectToRoute('admin_enseignants_view_classic');
        }
        return $this->render('admin/enseignants/enseignantCreate.html.twig',[
            'formEnseignant'=>$form->createView(),
            'enseignant' => $enseignant
        ]);
    }



}
