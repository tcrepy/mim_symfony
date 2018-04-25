<?php

namespace App\Repository;

use App\Entity\Post;
use App\Service\Tools;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function getTwoRandomElem()
    {
        //on recup le nombre de rows de la table
        $totalRowsTable = $this->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();
        $random_ids = Tools::UniqueRandomNumbersWithinRange(1,$totalRowsTable,2); //contient 2 id aléatoire entre 1 (id minimum de la table) et le nombre de row
        return $this->createQueryBuilder('p')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $random_ids)
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Post[] Returns an array of Post objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
