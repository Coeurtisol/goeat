<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findByStatutBySecteurByLivreur(string $statut, string $secteur, int $livreur)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->join('c.statut', 's')
            ->addSelect('s')
            ->where('c.statut = s.id')
            ->andWhere('s.nom = :nomtype')
            ->setParameter('nomtype', $statut)
            ->andwhere('c.ville = :ville')
            ->setParameter('ville', $secteur)
            ->andwhere('c.livreur = :livreur')
            ->setParameter('livreur', $livreur)
            ->getQuery()
            ->getResult();
    }

    public function findByStatutBySecteur(string $statut, string $secteur)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->join('c.statut', 's')
            ->addSelect('s')
            ->where('c.statut = s.id')
            ->andWhere('s.nom = :nomtype')
            ->setParameter('nomtype', $statut)
            ->andwhere('c.ville = :ville')
            ->setParameter('ville', $secteur)
            ->getQuery()
            ->getResult();
    }

    public function findByStatutByLivreur(string $statut, int $livreur)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->join('c.statut', 's')
            ->addSelect('s')
            ->where('c.statut = s.id')
            ->andWhere('s.nom = :nomtype')
            ->setParameter('nomtype', $statut)
            ->andwhere('c.livreur = :livreur')
            ->setParameter('livreur', $livreur)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Commande[] Returns an array of Commande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commande
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
