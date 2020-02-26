<?php

namespace App\Controller\admin;
use App\Form\AdminType;
use App\Form\SearchType;
use App\Repository\AbsenceRepository;
use App\Repository\AdminRepository;
use App\Repository\EnseignantRepository;
use App\Repository\EtudiantRepository;
use App\Repository\MatieresRepository;
use App\Repository\PromotionRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    //FONCTION POUR AFFICHER INFORMATIONS DANS LE DASHBOARD, PERMET DE METTRE L'ADMIN EN SESSION AUSSI
    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request,AdminRepository $repo,EnseignantRepository $repoEnseignant,PromotionRepository $repoPromo, EtudiantRepository $repoEtudiant, MatieresRepository $repoMatiere, AbsenceRepository $repoAbsence)
    {
        $session = new Session();

        //recup utilisateur en fonction de l'email en session
        $email = $this->getUser()->getEmail();
        $user = $repo->findOneByEmail($email);

        //enregistre en session la photo
        $session->set('user', $user);
        $photo = $user->getPhoto();
        $session->set('photo', $photo);
        $admin = $this->get('session')->get('user');

        //recupere tous les repo pour affichage page d'accueil
        $enseignants = $repoEnseignant->findAll();
        $promotions = $repoPromo->findAll();
        $etudiants = $repoEtudiant->findAll();
        $matieres = $repoMatiere->findAll();
        $absences = $repoAbsence->findAll();


        return $this->render('admin/index.html.twig', [
            'admin' => $admin,
            'enseignants' => $enseignants,
            'promotions' => $promotions,
            'etudiants' => $etudiants,
            'matieres' => $matieres,
            'absences' => $absences
        ]);
    }

    //FONCTION DE RECHERCHE POUR L'ADMIN
    /**
     * @Route("/admin/search", name="admin_search")
     */
    public function searchAdmin(Request $request, EtudiantRepository $repoEtudiant, MatieresRepository $repoMatiere, EnseignantRepository $repoEnseignant)
    {
        //recuperer la valeur taper en get
        $valeur = $request->get('valueSearch');

        //fonction de recherche dans etudiant en fonction de la valeur
        $listEtudiants = $repoEtudiant->findBy(
            array('nom' =>$valeur),
            array('nom'=>'desc'),
            50,
            0
        );
        //fonction de recherche dans enseignant en fonction de la valeur
        $listEnseignants = $repoEnseignant->findBy(
            array('nom'=>$valeur),
            array('nom'=>'desc'),
            50,
            0
        );

        //fonction de recherche dans matiere en fonction de la valeur
        $listMatieres = $repoMatiere->findBy(
            array('nom'=>$valeur),
            array('nom'=>'desc'),
            50,
            0
        );

        return $this->render('admin/search/search.html.twig',[
            'valueSearch' => $valeur,
            'etudiants'=>$listEtudiants,
            'matieres' => $listMatieres,
            'enseignants' => $listEnseignants

        ]);
    }


    //FONCTION POUR AFFICHER INFORMATION PROFIL ADMIN
    /**
     * @Route("/admin/profil", name="admin_profil")
     */
    public function profilAdmin(Request $request, AdminRepository $repoAdmin, ObjectManager $manager)
    {
        //recup utilisateur en fonction de l'email en session
        $email = $this->getUser()->getEmail();
        $admin = $repoAdmin->findOneByEmail($email);

        //recupere formulaire pour edit le profil admin
        $form = $this->createForm(AdminType::class,$admin);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($admin);
            $manager->flush();
            return $this->redirectToRoute('admin');
        }
        return $this->render('admin/profil/profil.html.twig',[
            'formAdmin'=>$form->createView(),
            'admin' => $admin
        ]);
    }

}
