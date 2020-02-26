<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\Enseignant;
use App\Entity\Etudiant;
use App\Entity\Login;
use App\Entity\Matiere;
use App\Entity\Messagerie;
use App\Entity\Promotion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class EtudiantFixtures extends Fixture
{
    //VA GENERER ALEATOIREMENT DES PROMOTIONS, ENSEIGNANTS, ETUDIANTS, MATIERES ET COURS

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        //CREATION PROMOTIONS x5
        for ($i=0;$i<5;$i++) {
            $promotion = new Promotion();
            $promotion->setLibelle($faker->word);

            //CREATION ENSEIGNANT x5
            for ($j = 0; $j < 8; $j++) {
                if($j == 0){
                    $adresse = new Adresse();
                    $adresse->setRue($faker->streetAddress)
                        ->setCodepostal($faker->postcode)
                        ->setVille($faker->city);


                    $enseignant = new Enseignant();
                    $messagerie = new Messagerie();
                    $enseignant->setNom($faker->lastName)
                        ->setPrenom($faker->firstName)
                        ->setEmail($enseignant->getPrenom().$enseignant->getNom()."@e-ecole.com")
                        ->setPhoto('https://imgplaceholder.com/300x300/cccccc/757575/fa-user')
                        ->setAdresse($adresse)
                        ->setMessagerie($messagerie)
                        ->addPromotion($promotion);

                    $login = new Login();
                    $login->setLogin('enseignant')
                        ->setEmail($enseignant->getPrenom().$enseignant->getNom()."@e-ecole.com")
                        ->setPassword('enseignant')
                        ->setRole('ROLE_ENSEIGNANT');


                    $messagerie->setEmail($enseignant->getEmail());
                    $enseignant->setLogin($login);
                    $manager->persist($enseignant);

                    //CREATION MATIERES par enseignant

                    $matiere = new Matiere();
                    $matiere->setNom($faker->word)
                        ->setPromotion($promotion)
                        ->setEnseignant($enseignant);

                    $manager->persist($matiere);

                    /*
                    //CREATION COURS  X6 par matiere
                    for ($e = 0; $e < 6; $e++) {
                        $cour = new Cours();
                        $cour->setMatiere($matiere)
                            ->setTitre($faker->word())
                            ->setDate(new \DateTime())
                            ->setDureeCour(new \DateTime())
                            ->setDescription($faker->sentence);

                        $manager->persist($cour);
                    }
                    */
                }
                else{
                    $adresse = new Adresse();
                    $adresse->setRue($faker->streetAddress)
                        ->setCodepostal($faker->postcode)
                        ->setVille($faker->city);


                    $enseignant = new Enseignant();
                    $messagerie = new Messagerie();
                    $enseignant->setNom($faker->lastName)
                        ->setPrenom($faker->firstName)
                        ->setEmail($enseignant->getPrenom().$enseignant->getNom()."@e-ecole.com")
                        ->setPhoto('https://imgplaceholder.com/300x300/cccccc/757575/fa-user')
                        ->setAdresse($adresse)
                        ->setMessagerie($messagerie)
                        ->addPromotion($promotion);

                    $login = new Login();
                    $login->setLogin($enseignant->getPrenom().".".$enseignant->getNom())
                        ->setEmail($enseignant->getPrenom().$enseignant->getNom()."@e-ecole.com")
                        ->setPassword($faker->password)
                        ->setRole('ROLE_ENSEIGNANT');


                    $messagerie->setEmail($enseignant->getEmail());
                    $enseignant->setLogin($login);
                    $manager->persist($enseignant);

                    //CREATION MATIERES par enseignant

                    $matiere = new Matiere();
                    $matiere->setNom($faker->word)
                        ->setPromotion($promotion)
                        ->setEnseignant($enseignant);

                    $manager->persist($matiere);

                    /*
                    //CREATION COURS  X6 par matiere
                    for ($e = 0; $e < 6; $e++) {
                        $cour = new Cours();
                        $cour->setMatiere($matiere)
                            ->setTitre($faker->word())
                            ->setDate(new \DateTime())
                            ->setDureeCour(new \DateTime())
                            ->setDescription($faker->sentence);

                        $manager->persist($cour);
                    }
                    */
                }


            }
            //CREATION ETUDIANT x20
            for ($k = 0; $k < 20; $k++) {
                if($k == 0){
                    $adresse = new Adresse();
                    $adresse->setRue($faker->streetAddress)
                        ->setCodepostal($faker->postcode)
                        ->setVille($faker->city);

                    $messagerie = new Messagerie();
                    $etudiant = new Etudiant();
                    $etudiant->setNom($faker->lastName)
                        ->setPrenom($faker->firstName)
                        ->setEmail($etudiant->getPrenom().$etudiant->getNom()."@e-ecole.com")
                        ->setDateNaissance(new \DateTime())
                        ->setPhoto('https://imgplaceholder.com/300x300/cccccc/757575/fa-user')
                        ->setPromotion($promotion)
                        ->setMessagerie($messagerie)
                        ->setAdresse($adresse);

                    $login = new Login();
                    $login->setLogin('etudiant')
                        ->setEmail($etudiant->getPrenom().$etudiant->getNom()."@e-ecole.com")
                        ->setPassword('etudiant')
                        ->setRole('ROLE_ETUDIANT');

                    $messagerie->setEmail($etudiant->getEmail());
                    $etudiant->setLogin($login);

                    $manager->persist($etudiant);
                    $manager->persist($promotion);

                }
                elseif ($k == 1){ //CREATION ROLE BLOG
                    $adresse = new Adresse();
                    $adresse->setRue($faker->streetAddress)
                        ->setCodepostal($faker->postcode)
                        ->setVille($faker->city);

                    $messagerie = new Messagerie();
                    $etudiant = new Etudiant();
                    $etudiant->setNom($faker->lastName)
                        ->setPrenom($faker->firstName)
                        ->setEmail($etudiant->getPrenom().$etudiant->getNom()."@e-ecole.com")
                        ->setDateNaissance(new \DateTime())
                        ->setPhoto('https://imgplaceholder.com/300x300/cccccc/757575/fa-user')
                        ->setPromotion($promotion)
                        ->setMessagerie($messagerie)
                        ->setAdresse($adresse);

                    $login = new Login();
                    $login->setLogin('blog')
                        ->setEmail($etudiant->getPrenom().$etudiant->getNom()."@e-ecole.com")
                        ->setPassword('blog')
                        ->setRole('ROLE_BLOG');

                    $messagerie->setEmail($etudiant->getEmail());
                    $etudiant->setLogin($login);

                    $manager->persist($etudiant);
                    $manager->persist($promotion);
                }
                else{
                    $adresse = new Adresse();
                    $adresse->setRue($faker->streetAddress)
                        ->setCodepostal($faker->postcode)
                        ->setVille($faker->city);

                    $messagerie = new Messagerie();
                    $etudiant = new Etudiant();
                    $etudiant->setNom($faker->lastName)
                        ->setPrenom($faker->firstName)
                        ->setEmail($etudiant->getPrenom().$etudiant->getNom()."@e-ecole.com")
                        ->setDateNaissance(new \DateTime())
                        ->setPhoto('https://imgplaceholder.com/300x300/cccccc/757575/fa-user')
                        ->setPromotion($promotion)
                        ->setMessagerie($messagerie)
                        ->setAdresse($adresse);

                    $login = new Login();
                    $login->setLogin($etudiant->getPrenom().".".$etudiant->getNom())
                        ->setEmail($etudiant->getPrenom().$etudiant->getNom()."@e-ecole.com")
                        ->setPassword($faker->password)
                        ->setRole('ROLE_ETUDIANT');

                    $messagerie->setEmail($etudiant->getEmail());
                    $etudiant->setLogin($login);

                    $manager->persist($etudiant);
                    $manager->persist($promotion);
                }
            }
        }
        $manager->flush();
    }
}
