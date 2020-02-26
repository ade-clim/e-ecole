<?php

namespace App\Controller\etudiant;

use App\Entity\Matiere;
use App\Entity\MessageRecu;
use App\Entity\MessageSend;
use App\Form\MessageSendType;
use App\Repository\EtudiantRepository;
use App\Repository\MessagerieRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MatiereController extends AbstractController
{

    //FONCTION POUR AFFICHER MATIERE EN FONCTION DE L'ETUDIANT
    /**
     * @Route("/etudiant/matiere{id}/view", name="etu_matiere_view")
     */
    public function coursByMatiere(Request $request, Matiere $matiere,MessagerieRepository $repoMessagerie,ObjectManager $manager, EtudiantRepository $repoEtudiant)
    {
        //recuperer l'utilisateur en session
        $email = $this->getUser()->getEmail();
        $etudiant = $repoEtudiant->findOneByEmail($email);

        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //enregistre le message dans la messagerie de l'etudiant
            $messageSend->setMessagerie($etudiant->getMessagerie());
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
            return $this->redirectToRoute('etu_matiere_view',array('id'=>$matiere->getId()));
        }
        return $this->render('etudiant/matiere/matiereShow.html.twig', [
            'formMessageSend'=>$form->createView(),
            'matieres' => $matiere,

        ]);
    }
}
