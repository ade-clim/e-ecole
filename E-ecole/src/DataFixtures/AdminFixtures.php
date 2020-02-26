<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Adresse;
use App\Entity\Login;
use App\Entity\Messagerie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AdminFixtures extends Fixture
{
    //VA GENERER ALEATOIREMENT UN ADMIN
    public function load(ObjectManager $manager)
    {
        $messagerie = new Messagerie();
        $admin = new Admin();
        $adresse = new Adresse();
        $faker = Factory::create('fr_FR');



        $adresse->setRue("rue tartanpion")
            ->setCodepostal("59000")
            ->setVille("Lille");

        $admin->setNom("roger")
            ->setPrenom("marcel")
            ->setEmail($admin->getPrenom().$admin->getNom()."@e-ecole.com")
            ->setAdresse($adresse)
            ->setMessagerie($messagerie)
            ->setPhoto('https://imgplaceholder.com/300x300/cccccc/757575/fa-user');

        $login = new Login();
        $login->setLogin('admin')
            ->setEmail($admin->getPrenom().$admin->getNom()."@e-ecole.com")
            ->setPassword('admin')
            ->setRole('ROLE_ADMIN');

        $messagerie->setEmail($admin->getEmail());
        $admin->setLogin($login);
        $manager->persist($admin);
        $manager->flush();


    }
}
