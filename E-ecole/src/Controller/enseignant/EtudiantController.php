<?php

namespace App\Controller\enseignant;

use App\Entity\Etudiant;
use App\Entity\MessageRecu;
use App\Entity\MessageSend;
use App\Form\MessageSendType;
use App\Repository\EnseignantRepository;
use App\Repository\MessagerieRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{

    //FONCTION POUR VOIR ETUDIANT EN VUE CLASSIC
    /**
     * @Route("/enseignant/etudiants/classic", name="ens_etudiant_view_classic")
     */
    public function index(Request $request,PaginatorInterface $paginator,EnseignantRepository $repo)
    {
        $email = $this->getUser()->getEmail();
        $session = new Session();
        $user = $repo->findOneByEmail($email);

        $session->set('user', $user);

        $promo = $user->getPromotions();

        for ($i=0;$i<sizeof($promo);$i++){
            $etudiants[] = $promo[$i]->getEtudiants();
        }
        $etudiant = $paginator->paginate(
            $etudiants,
            $request->query->getInt('page', 1),
            1
        );
        return $this->render('enseignant/etudiants/etudiantViewClassic.html.twig',[
            'etudiant' => $etudiant,

        ]);
    }

    //FONCTION POUR VOIR ETUDIANT EN VUE AVANCEE
    /**
     * @Route("/enseignant/etudiants/avancee", name="ens_etudiant_view_avancee")
     */
    public function ListeEtudiantsAvancee(Request $request,PaginatorInterface $paginator,EnseignantRepository $repo)
    {
        $email = $this->getUser()->getEmail();
        $session = new Session();
        $user = $repo->findOneByEmail($email);

        $session->set('user', $user);

        $promo = $user->getPromotions();

        for ($i=0;$i<sizeof($promo);$i++){
            $etudiants[] = $promo[$i]->getEtudiants();
        }
        $etudiant = $paginator->paginate(
            $etudiants,
            $request->query->getInt('page', 1),
            1
        );

        return $this->render('enseignant/etudiants/etudiantViewAvancee.html.twig',[
            'etudiant' => $etudiant,
        ]);
    }

    //FONCTION POUR AFFICHER ETUDIANT PAR ID
    /**
     * @Route("/enseignant/etudiants/{id}", name="ens_etudiant_show")
     */
    public function showEtudiant(Request $request,Etudiant $etudiant, MessagerieRepository $repoMessagerie, ObjectManager $manager, EnseignantRepository $repoEnseignant){

        $email = $this->getUser()->getEmail();
        $enseignant = $repoEnseignant->findOneByEmail($email);

        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($enseignant->getMessagerie());
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
            return $this->redirectToRoute('ens_etudiant_show',array('id'=>$etudiant->getId()));
        }
        return $this->render('enseignant/etudiants/etudiantShow.html.twig',[
            'formMessageSend'=>$form->createView(),
            'etudiant' => $etudiant,
        ]);
    }
}
