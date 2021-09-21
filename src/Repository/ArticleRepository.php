<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    

    public function getArticleByName($articleName)
    {
        return $this->createQueryBuilder('a') // le 'a' est un alias
                    ->andWhere('a.title Like :val') //:val est un parametre
                    ->setParameter('val', '%'.$articleName . '%') // permet de lier le parametre val a ma variable $articleName
                    ->orderBy('a.title', 'ASC')//ordonner par le nom des articles dans l'ordre alphabetique
                    ->getQuery()
                    ->getResult();
                    //ces deux dernieres lignes permettent de recuperer le resultat de la requete(ici, un tableau d'articles)

    }

    public function getArticlesOrderByDate()
    {
        return $this->createQueryBuilder('a')
            	    ->orderBy('a.createdAt', 'DESC')
                    ->getQuery()
                    ->getResult();
    }




 /*

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    *

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
