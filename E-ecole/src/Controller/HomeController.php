<?php

namespace App\Controller;

use App\Entity\MessageRecu;
use App\Entity\MessageSend;
use App\Form\ArticleType;
use App\Form\MessageSendType;
use App\Repository\AdminRepository;
use App\Repository\MessagerieRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    //FONCTION PAGE ACCUEIL HOME
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, ObjectManager $manager,AdminRepository $repoAdmin, MessagerieRepository $repoMessagerie)
    {
        //je recupere l'admin comme destinataire pour l'envoie du message en page d'accueil
        $allAdmin = $repoAdmin->findAll();
        $admin = $allAdmin[0];
        $contact = new MessageSend();
        $form = $this->createForm(MessageSendType::class,$contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //recup l'id messagerie de l'admin
            $messagerieId = $repoMessagerie->findOneByEmail($admin->getEmail());

            $contact->setMessagerie(null);
            $contact->setDate(new \DateTime('now'));

            // on creer le message qui servira de message recu a l'utilisateur ici l'admin
            $messageRecu = new MessageRecu();
            $messageRecu->setEmail($contact->getEmail());
            $messageRecu->setTitre($contact->getTitre());
            $messageRecu->setBody($contact->getBody());
            $messageRecu->setDate($contact->getDate());
            $messageRecu->setMessagerie($messagerieId);
            $messageRecu->setOnline(1);


            $manager->persist($messageRecu);
            $manager->persist($contact);
            $manager->flush();

        }

        return $this->render('home/index.html.twig', [
            'formContact'=>$form->createView()
        ]);
    }
}
