<?php

namespace App\Controller\admin;
use App\Entity\Login;
use App\Entity\Etudiant;
use App\Entity\MessageRecu;
use App\Entity\Messagerie;
use App\Entity\MessageSend;
use App\Form\EtudiantType;
use App\Form\MessageSendType;
use App\Repository\AdminRepository;
use App\Repository\EtudiantRepository;
use App\Repository\MessagerieRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{

    //FONCTION POUR AFFICHER ETUDIANT EN VUE CLASSIC
    /**
     * @Route("/admin/etudiants/classic", name="admin_etudiants_view_classic")
     */
    public function ListeEtudiantsClassic(Request $request,PaginatorInterface $paginator,EtudiantRepository $repo)
    {

        $etudiant = $paginator->paginate(
            $repo->findAll(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/etudiants/etudiantsViewClassic.html.twig', [
            'controller_name' => 'AdminController',
            'etudiant'=>$etudiant
        ]);
    }

    //FONCTION POUR AFFICHER ETUDIANT EN VUE AVANCEE
    /**
     * @Route("/admin/etudiants/avancee", name="admin_etudiants_view_avancee")
     */
    public function ListeEtudiantsAvancee(EtudiantRepository $repo,Request $request,PaginatorInterface $paginator)
    {
        $etudiant = $paginator->paginate(
            $repo->findAll(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/etudiants/etudiantsViewAvancee.html.twig', [
            'controller_name' => 'AdminController',
            'etudiant'=>$etudiant
        ]);
    }


    //FONCTION POUR MODIFIER ETUDIANT
    /**
     * @Route("/admin/etudiant/{id}/edit", name="admin_etudiant_edit")
     */
    public function editEtudiant(Etudiant $etudiant, Request $request, ObjectManager $manager){

        $form = $this->createForm(EtudiantType::class,$etudiant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($etudiant);
            $manager->flush();
            return $this->redirectToRoute('admin_etudiant_show',array('id'=>$etudiant->getId()));
        }
        return $this->render('admin/etudiants/etudiantEdit.html.twig',[
            'formEtudiant'=>$form->createView(),
            'etudiant' => $etudiant
        ]);
    }

    //FONCTION POUR DELETE ETUDIANT PAR ID
    /**
     * @Route("/admin/etudiant/{id}/delete", name="admin_etudiant_delete")
     */
    public function deleteEtudiant(Etudiant $etudiant, ObjectManager $manager){

        $manager->remove($etudiant);
        $manager->flush();
        return $this->redirectToRoute('admin_etudiants_view_avancee');
    }

    //FONCTION POUR AFFICHER ETUDIANT PAR ID
    /**
     * @Route("/admin/etudiant/{id}", name="admin_etudiant_show")
     */
    public function showEtudiant(Request $request, Etudiant $etudiant, MessagerieRepository $repoMessagerie, ObjectManager $manager,AdminRepository $repoAdmin){

        $email = $this->getUser()->getEmail();
        $admin = $repoAdmin->findOneByEmail($email);

        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($admin->getMessagerie());
            $messageSend->setDate(new \DateTime('now'));

            //on s'occupe maintenant d'enregistrer le message dans "messagerecu" de l'utilisateur concerner;

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
            return $this->redirectToRoute('admin_etudiant_show',array('id'=>$etudiant->getId()));
        }
        return $this->render('admin/etudiants/etudiantShow.html.twig',[
            'formMessageSend'=>$form->createView(),
            'etudiant' => $etudiant
        ]);
    }


    //FONCTION POUR CREER ETUDIANT
    /**
     * @Route("/admin/etudiants/create", name="admin_etudiant_create")
     */
    public function createEtudiant(Request $request, ObjectManager $manager){

        $faker = Factory::create('fr_FR');
        $etudiant = new Etudiant();

        $form = $this->createForm(EtudiantType::class,$etudiant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $login = new Login();
            $login->setLogin($etudiant->getPrenom().$etudiant->getNom()."@e-ecole.com");
            $login->setPassword($faker->password);
            $login->setEmail($etudiant->getPrenom().".".$etudiant->getNom()."@e-ecole.com");

            $login->setRole('ROLE_ETUDIANT');
            $etudiant->setLogin($login);

            // creer messagerie pour l'etudiant
            $messagerie = new Messagerie();
            $messagerie->setEmail($login->getEmail());
            $etudiant->setMessagerie($messagerie);

            $etudiant->setEmail($login->getEmail());
            $manager->persist($etudiant);
            $manager->flush();
            return $this->redirectToRoute('admin_etudiants_view_classic');
        }
        return $this->render('admin/etudiants/etudiantCreate.html.twig',[
            'formEtudiant'=>$form->createView(),
            'etudiant' => $etudiant
        ]);
    }

}
