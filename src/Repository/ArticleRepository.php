<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findByCategory(Category $category)
        {
            return $this->createQueryBuilder('a')
                ->innerJoin('a.categories', 'c') // Liaison avec la relation "categories"
                ->andWhere('c = :category') // Filtrage par la catÃ©gorie
                ->setParameter('category', $category)
                ->getQuery()
                ->getResult();
        }

    public function findAllWithAuthors(): array
{
    return $this->createQueryBuilder('a')
        ->leftJoin('a.author', 'u') // Jointure avec l'entité User
        ->addSelect('u') // Inclure les données de l'auteur dans la requête
        ->getQuery()
        ->getResult();
}

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
