<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    /**
     * @param array{titre: string, auteur: int, editeur: int, categorie: int} $criteria
     * @return Livre[]
     */
    public function searchByCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.auteur', 'a')
            ->leftJoin('l.editeur', 'e')
            ->leftJoin('l.categorie', 'c')
            ->addSelect('a', 'e', 'c')
        ;

        if ($criteria['titre'] !== '') {
            $qb->andWhere('LOWER(l.titre) LIKE :titre')
                ->setParameter('titre', '%'.strtolower($criteria['titre']).'%');
        }

        if ($criteria['auteur'] > 0) {
            $qb->andWhere('a.id = :auteur')
                ->setParameter('auteur', $criteria['auteur']);
        }

        if ($criteria['editeur'] > 0) {
            $qb->andWhere('e.id = :editeur')
                ->setParameter('editeur', $criteria['editeur']);
        }

        if ($criteria['categorie'] > 0) {
            $qb->andWhere('c.id = :categorie')
                ->setParameter('categorie', $criteria['categorie']);
        }

        return $qb->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Livre[] Returns an array of Livre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Livre
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
