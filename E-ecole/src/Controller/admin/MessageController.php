<?php

namespace App\Controller\admin;
use App\Entity\MessageRecu;
use App\Entity\MessageSend;
use App\Form\MessageSendType;
use App\Repository\AdminRepository;
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
     * @Route("/admin/messagerie", name="admin_messagerie")
     */
    public function index(Request $request, ObjectManager $manager, AdminRepository $repoAdmin, MessagerieRepository $repoMessagerie)
    {
        $email = $this->getUser()->getEmail();
        $admin = $repoAdmin->findOneByEmail($email);

        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($admin->getMessagerie());
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
            return $this->redirectToRoute('admin_messagerie');
        }

        return $this->render('admin/messages/messages.html.twig', [
            'formMessageSend'=>$form->createView(),
            'admin' => $admin
        ]);
    }

    //FONCTION POUR DELETE MESSAGE INBOX
    /**
     * @Route("/admin/message/inbox/delete", name="admin_message_inbox_delete")
     */
    public function deleteInboxMessage(Request $request, ObjectManager $manager,  MessageRecuRepository $repoMessageRecu)
    {
        $allId = $request->get('mesIds', []);
        foreach ($allId as $value){
            $manager->remove($repoMessageRecu->findOneById($value));
        }

        $manager->flush();
        return $this->redirectToRoute('admin_messagerie');
    }


    //FONCTION POUR DELETE MESSAGE ENVOYER

    /**
     * @Route("/admin/message/send/delete", name="admin_message_send_delete")
     */
    public function deleteSendMessage(Request $request, ObjectManager $manager,MessageSendRepository $repoMessageSend)
    {
        $allId = $request->get('mesIds', []);
        foreach ($allId as $value){
            $manager->remove($repoMessageSend->findOneById($value));
        }

        $manager->flush();
        return $this->redirectToRoute('admin_message_send');
    }


    //FONCTION POUR AFFICHER MESSAGE INBOX PAR ID
    /**
     * @Route("/admin/message/inbox/{id}/view", name="admin_message_inbox_view")
     */
    public function viewInboxMessage(MessageRecu $message,Request $request, ObjectManager $manager, AdminRepository $repoAdmin, MessagerieRepository $repoMessagerie)
    {
        //on recuperer l'utilisateur en session
        $email = $this->getUser()->getEmail();
        $admin = $repoAdmin->findOneByEmail($email);

        //on va affecter la valeur online a 0 pour indiquer que le message et lu et changer sa couleur
        $message->setOnline(0);
        $manager->persist($message);
        $manager->flush();

        //va permettre d'envoyer un nouveau message en recuperant l'expediteur et le destinataire
        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($admin->getMessagerie());
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
            return $this->redirectToRoute('admin_messagerie');
        }
        return $this->render('admin/messages/messageShow.html.twig',[
            'formMessageSend'=>$form->createView(),
            'message'=>$message
        ]);
    }



    //FONCTION POUR AFFICHER MESSAGE ENVOYER PAR ID
    /**
     * @Route("/admin/message/send/{id}/view", name="admin_message_send_view")
     */
    public function viewSendMessage(MessageSend $message,Request $request, ObjectManager $manager, AdminRepository $repoAdmin, MessagerieRepository $repoMessagerie)
    {
        //on recuperer l'utilisateur en session
        $email = $this->getUser()->getEmail();
        $admin = $repoAdmin->findOneByEmail($email);

        //va permettre d'envoyer un nouveau message en recuperant l'expediteur et le destinataire
        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($admin->getMessagerie());
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
            return $this->redirectToRoute('admin_messagerie');
        }
        return $this->render('admin/messages/messageShow.html.twig',[
            'formMessageSend'=>$form->createView(),
            'message'=>$message
        ]);
    }



    //FONCTION POUR ENVOYER MESSAGE
    /**
     * @Route("/admin/message/send", name="admin_message_send")
     */
    public function sendMessage(Request $request, ObjectManager $manager, AdminRepository $repoAdmin, MessagerieRepository $repoMessagerie)
    {
        //on recuperer l'utilisateur en session
        $email = $this->getUser()->getEmail();
        $admin = $repoAdmin->findOneByEmail($email);

        //va permettre d'envoyer un nouveau message en recuperant l'expediteur et le destinataire
        $messageSend = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$messageSend);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $messageSend->setMessagerie($admin->getMessagerie());
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
            return $this->redirectToRoute('admin_messagerie');
        }
        return $this->render('admin/messages/messagesSend.html.twig',[
            'formMessageSend'=>$form->createView(),
            'admin' => $admin
        ]);
    }

}
