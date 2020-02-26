<?php

namespace App\Controller\etudiant;

use App\Form\EtudiantProfilType;
use App\Repository\EtudiantRepository;
use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{
    //FONCTION ETUDIANT PAGE ACCUEIL
    /**
     * @Route("/etudiant", name="etudiant")
     */
    public function index(Request $request, PaginatorInterface $paginator,EtudiantRepository $repo, PostRepository $repoArticle )
    {
        $email = $this->getUser()->getEmail();
        $session = new Session();
        $user = $repo->findOneByEmail($email);
        $photo = $user->getPhoto();
        $session->set('photo', $photo);
        $session->set('user', $user);
        $etudiant = $this->get('session')->get('user');

        $mesAbsences = $user->getAbsences();
        $absences = $paginator->paginate(
            $mesAbsences,
            $request->query->getInt('page', 1),
            1
        );
        $articlesBlog = $repoArticle->findBy(
            array('online' => '1'),
            array('id'=>'desc'),
            10
        );

        return $this->render('etudiant/index.html.twig', [
            'etudiant' => $etudiant,
            'absences'=>$absences,
            'article' => $articlesBlog
        ]);
    }

    //FONCTION POUR MODIFIER ET VOIR PROFIL
    /**
     * @Route("/etudiant/profil", name="etu_profil")
     */
    public function profilEtudiant(Request $request, EtudiantRepository $repoEtudiant, ObjectManager $manager)
    {
        $email = $this->getUser()->getEmail();
        $etudiant = $repoEtudiant->findOneByEmail($email);

        $form = $this->createForm(EtudiantProfilType::class,$etudiant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($etudiant);
            $manager->flush();
            return $this->redirectToRoute('etudiant');
        }
        return $this->render('etudiant/profil/profil.html.twig',[
            'formEtudiant'=>$form->createView(),
            'etudiant' => $etudiant
        ]);
    }
}
