<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    //VA GENERER ALEATOIREMENT DES ARTICLES POUR LE BLOG
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i=0;$i<3;$i++){

            $categorie = new Category();
            $categorie->setName($faker->word)
                ->setSlug($categorie->getName());
            $manager->persist($categorie);

            for ($z=0;$z<5;$z++){

                $article = new Post();
                $article->setName($faker->sentence(3))
                    ->setSlug($article->getName())
                    ->setContent($faker->sentence(6))
                    ->setCategory($categorie)
                    ->setOnline('1')
                    ->setPhoto('http://lorempixel.com/500/500/nightlife');
                    $manager->persist($article);
            }
        }


        $manager->flush();
    }
}
