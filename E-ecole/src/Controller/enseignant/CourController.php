<?php

namespace App\Controller\enseignant;

use App\Entity\Absence;
use App\Entity\Cours;
use App\Entity\Matiere;
use App\Entity\Ressource;
use App\Form\AbsenceType;
use App\Form\CoursType;
use App\Form\RessourceType;
use App\Repository\AbsenceRepository;
use App\Repository\EnseignantRepository;
use App\Repository\EtudiantRepository;
use App\Repository\RessourceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CourController extends AbstractController
{

    //FONCTION POUR AFFICHER COURS
    /**
     * @Route("/enseignant/cour", name="ens_cour")
     */
    public function index(EnseignantRepository $repo)
    {
        $email = $this->getUser()->getEmail();
        $session = new Session();
        $user = $repo->findOneByEmail($email);
        $session->set('user', $user);
        $enseignant = $this->get('session')->get('user');


        $matieres = $user->getMatiere();
        for ($i=0;$i<sizeof($matieres);$i++){
            $cours[] = $matieres[$i]->getCours();
        }

        return $this->render('enseignant/cour/courShow.html.twig', [
            'matiere'=>$matieres,
            'enseignant' => $enseignant,
        ]);
    }

    //FONCTION POUR CREER COURS
    /**
     * @Route("/enseignant/cour/{id}/create", name="ens_cour_create")
     */
    public function createCour(Matiere $matiere ,Request $request, ObjectManager $manager)
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class,$cours);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $cours->setMatiere($matiere);
            $manager->persist($cours);
            $manager->flush();
            return $this->redirectToRoute('ens_cour');
        }

        return $this->render('enseignant/cour/courCreate.html.twig', [
            'formCour'=>$form->createView(),
            'enseignant' => $cours
        ]);
    }

    //FONCTION POUR AFFICHER COURS PAR ID
    /**
     * @Route("/enseignant/cour/{id}/view", name="ens_cour_view")
     */
    public function editCour(Cours $cour ,Request $request, ObjectManager $manager, RessourceRepository $repo, EtudiantRepository $repoEtudiant)
    {
        $etudiants = $cour->getMatiere()->getPromotion()->getEtudiants();

        $ressource = new Ressource();
        $formRessource = $this->createForm(RessourceType::class, $ressource);

        $formRessource->handleRequest($request);

        if($formRessource->isSubmitted() && $formRessource-> isValid()){
            $file = $ressource->getNom();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $fileName);
            $ressource->setNom($fileName);

            $cour->addRessource($ressource);
            $manager->persist($ressource);
            $manager->flush();
            return $this->redirectToRoute('ens_cour_view', array('id'=>$cour->getId()));
        }


        return $this->render('enseignant/cour/courView.html.twig', [
            'formRessource' => $formRessource->createView(),
            'etudiants' => $etudiants,
            'cour' => $cour
        ]);
    }

    //FONCTION POUR CREER ABSENCES PAR COURS
    /**
     * @Route("/enseignant/cour/{id}/absence/create", name="ens_cour_absence")
     */
    public function createAbsenceCour(Cours $cour, ObjectManager $manager,Request $request,EtudiantRepository $repoEtudiant, AbsenceRepository $absencesRepo)
    {

        //supprime les absences des etudiants non checked
        $absencesCours = $cour->getAbsences();
        foreach ($absencesCours as $value){
            $manager->remove($value);
        }


        //formulaire recupere information absences
        $absence = new Absence();
        $formAbsence = $this->createForm(AbsenceType::class, $absence);
        $formAbsence->handleRequest($request);

        //recupere les ids checkbox absences et motif absence
        $allId = $request->get('mesIds', []);
        $motifAbsence = $request->get('motifAbsence');

        //enregistre les etudiants correpondant a l'id checked
        $etudiant = [];
        foreach ($allId as $value){
            $etudiant[] = $repoEtudiant->findOneById($value);
        }

        // creer absences pour chaque etudiants checked
        for ($i =0;$i<sizeof($etudiant);$i++){
            $absence = new Absence();
            $absence->setCours($cour);
            $absence->setEtudiant($etudiant[$i]);
            $absence->setMotif('absence');
            $manager->persist($absence);
        }
        $manager->flush();

        return $this->redirectToRoute('ens_cour');

        return $this->render('enseignant/cour/courAbsence.html.twig', [
            'formAbsence'=>$formAbsence->createView(),
        ]);
    }

}
