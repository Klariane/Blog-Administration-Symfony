<?php

namespace App\DataFixtures;

use Faker;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');




        for($j=1; $j<=3; $j++){//creation de 3 categories
            $category = new Category;
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());
            $manager->persist($category);        

        for($i = 1; $i <= mt_rand(4,6); $i++)
        {
            $article = new Article;//instancier la classe Article
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>'; //generer des paragraphe

            $t = $faker->dateTimeBetween('-6 months');//on stocke une fausse date
            $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($t) // on affecte la fausse date à la création et à la mise à jour
                    ->setUpdatedAt($t)
                    ->setCategory($category);
                   

                    $manager->persist($article); //preparer linsertion des donnée en bdd

             for($k = 1; $k<=mt_rand(4,10); $k++) 
             {
                 $comment = new Comment;
                 $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>'; //generer des paragraphe

// $now = new \DateTime(); // objet datetime avec l'heure et la date du jour
                    // $interval = $now->diff($article->getCreatedAt()); // représente l'intervalle entre maintenant et la date de création de l'article
                    // $days = $interval->days; // nombre de jours entre maintenant et la date de création de l'article
                    // $minimum = '-'. $days . ' days';


                 $days= (new \DateTime())->diff($article->getCreatedAt())->days;
                 $comment->setAuthor($faker->name)
                         ->setContent($content)
                         ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                         ->setArticle($article);
                         
                    $manager->persist($comment);     

             }      
        }
    }

        $manager->flush();// inserer les articles en BDD
    }
}
