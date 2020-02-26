<?php

namespace App\Controller\enseignant;

use App\Form\EnseignantProfilType;
use App\Repository\EnseignantRepository;
use App\Repository\EtudiantRepository;
use App\Repository\MatieresRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class EnseignantController extends AbstractController
{
    //FONCTION ACCUEIL ENSEIGNANT
    /**
     * @Route("/enseignant/", name="enseignant")
     */
    public function index(EnseignantRepository $repo)
    {
        $email = $this->getUser()->getEmail();
        $session = new Session();
        $user = $repo->findOneByEmail($email);
        $photo = $user->getPhoto();
        $session->set('user', $user);
        $session->set('photo', $photo);

        $enseignant = $this->get('session')->get('user');

        return $this->render('enseignant/index.html.twig', [
            'enseignant' => $enseignant
        ]);
    }

    //FONCTION RECHERCHE PAGE ENSEIGNANT
    /**
     * @Route("/enseignant/search", name="ens_search")
     */
    public function searchEnseignant(Request $request, EtudiantRepository $repoEtudiant, MatieresRepository $repoMatiere, EnseignantRepository $repoEnseignant)
    {
        $valeur = $request->get('valueSearch');

        $listEtudiants = $repoEtudiant->findBy(
            array('nom' =>$valeur),
            array('nom'=>'desc'),
            50,
            0
        );


        $listEnseignants = $repoEnseignant->findBy(
            array('nom'=>$valeur),
            array('nom'=>'desc'),
            50,
            0
        );

        $listMatieres = $repoMatiere->findBy(
            array('nom'=>$valeur),
            array('nom'=>'desc'),
            50,
            0
        );

        return $this->render('enseignant/search/search.html.twig',[
            'valueSearch' => $valeur,
            'etudiants'=>$listEtudiants,
            'matieres' => $listMatieres,
            'enseignants' => $listEnseignants

        ]);
    }

    //FONCTION VOIR ET MODIFIER PROFIL ENSEIGNANT
    /**
     * @Route("/enseignant/profil", name="ens_profil")
     */
    public function profilEnseignant(Request $request, EnseignantRepository $repoEnseignant, ObjectManager $manager)
    {
        $email = $this->getUser()->getEmail();
        $enseignant = $repoEnseignant->findOneByEmail($email);

        $form = $this->createForm(EnseignantProfilType::class,$enseignant);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($enseignant);
            $manager->flush();
            return $this->redirectToRoute('enseignant');
        }
        return $this->render('enseignant/profil/profil.html.twig',[
            'formEnseignant'=>$form->createView(),
            'enseignant' => $enseignant
        ]);
    }
}
