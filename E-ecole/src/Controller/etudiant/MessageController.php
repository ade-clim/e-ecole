<?php

namespace App\Controller\etudiant;

use App\Entity\MessageRecu;
use App\Entity\MessageSend;
use App\Form\MessageSendType;
use App\Repository\EtudiantRepository;
use App\Repository\MessageRecuRepository;
use App\Repository\MessagerieRepository;
use App\Repository\MessageSendRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{

    //FONCTION POUR AFFICHER MESSAGERIE
    /**
    * @Route("/etudiant/messagerie", name="etu_messagerie")
    */
    public function index(Request $request, ObjectManager $manager, EtudiantRepository $repoEtudiant, MessagerieRepository $repoMessagerie)
    {
        $email = $this->getUser()->getEmail();
        $etudiant = $repoEtudiant->findOneByEmail($email);

        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($etudiant->getMessagerie());
            $messageSend->setDate(new \DateTime('now'));

            //on s'occupe maintenant d'enregistrer le message dans "messagerecu";

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
            return $this->redirectToRoute('etu_messagerie');
        }

        return $this->render('etudiant/messages/messages.html.twig', [
            'formMessageSend'=>$form->createView(),
            'etudiant' => $etudiant
        ]);
    }


    //FONCTION POUR DELETE MESSAGE INBOX
    /**
     * @Route("/etudiant/message/inbox/delete", name="etu_message_inbox_delete")
     */
    public function deleteInboxMessage(Request $request, ObjectManager $manager,  MessageRecuRepository $repoMessageRecu)
    {
        $allId = $request->get('mesIds', []);
        foreach ($allId as $value){
           $manager->remove($repoMessageRecu->findOneById($value));
        }

        $manager->flush();
        return $this->redirectToRoute('etu_messagerie');
    }


    //FONCTION POUR DELETE MESSAGE SEND
    /**
     * @Route("/etudiant/message/send/delete", name="etu_message_send_delete")
     */
    public function deleteSendMessage(Request $request, ObjectManager $manager,MessageSendRepository $repoMessageSend)
    {
        $allId = $request->get('mesIds', []);
        foreach ($allId as $value){
            $manager->remove($repoMessageSend->findOneById($value));
        }
        $manager->flush();
        return $this->redirectToRoute('etu_message_send');
    }


    //FONCTION POUR AFFICHER MESSAGE INBOX PAR ID
    /**
     * @Route("/etudiant/message/inbox/{id}/view", name="etu_message_inbox_view")
     */
    public function viewInboxMessage(MessageRecu $message,Request $request, ObjectManager $manager, EtudiantRepository $repoEtudiant, MessagerieRepository $repoMessagerie)
    {
        //on recuperer l'utilisateur en session
        $email = $this->getUser()->getEmail();
        $etudiant = $repoEtudiant->findOneByEmail($email);

        //on va affecter la valeur online a 0 pour indiquer que le message et lu et changer sa couleur
        $message->setOnline(0);
        $manager->persist($message);
        $manager->flush();

        //va permettre d'envoyer un nouveau message en recuperant l'expediteur et le destinataire
        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($etudiant->getMessagerie());
            $messageSend->setDate(new \DateTime('now'));

            //on s'occupe maintenant d'enregistrer le message dans "messagerecu";

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
            return $this->redirectToRoute('etu_messagerie');
        }
        return $this->render('etudiant/messages/messageShow.html.twig',[
            'formMessageSend'=>$form->createView(),
            'message'=>$message
        ]);
    }



    //FONCTION POUR AFFICHER MESSAGE ENVOYER PAR ID
    /**
     * @Route("/etudiant/message/send/{id}/view", name="etu_message_send_view")
     */
    public function viewSendMessage(MessageSend $message,Request $request, ObjectManager $manager, EtudiantRepository $repoEtudiant, MessagerieRepository $repoMessagerie)
    {
        //on recuperer l'utilisateur en session
        $email = $this->getUser()->getEmail();
        $etudiant = $repoEtudiant->findOneByEmail($email);

        //va permettre d'envoyer un nouveau message en recuperant l'expediteur et le destinataire
        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($etudiant->getMessagerie());
            $messageSend->setDate(new \DateTime('now'));

            //on s'occupe maintenant d'enregistrer le message dans "message recu";

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
            return $this->redirectToRoute('etu_messagerie');
        }
        return $this->render('etudiant/messages/messageShow.html.twig',[
            'formMessageSend'=>$form->createView(),
            'message'=>$message
        ]);
    }



    //FONCTION POUR ENVOYER MESSAGE
    /**
     * @Route("/etudiant/message/send", name="etu_message_send")
     */
    public function sendMessage(Request $request, ObjectManager $manager, EtudiantRepository $repoEtudiant, MessagerieRepository $repoMessagerie)
    {
        //on recuperer l'utilisateur en session
        $email = $this->getUser()->getEmail();
        $etudiant = $repoEtudiant->findOneByEmail($email);

        //va permettre d'envoyer un nouveau message en recuperant l'expediteur et le destinataire
        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($etudiant->getMessagerie());
            $messageSend->setDate(new \DateTime('now'));

            //on s'occupe maintenant d'enregistrer le message dans "messagerecu";

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
            return $this->redirectToRoute('etu_messagerie');
        }
        return $this->render('etudiant/messages/messagesSend.html.twig',[
            'formMessageSend'=>$form->createView(),
            'etudiant' => $etudiant
        ]);
    }

}
