<?php

namespace App\Repository;

use App\Entity\Film;
use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seance>
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }
    public function findByFilters(?string $filmTitle, ?string $date): array
    {
        $qb = $this->createQueryBuilder('s')
            ->join('s.film', 'f')
            ->where('s.dateHeureDebut >= :now') // Toujours ne prendre que les séances à venir
            ->setParameter('now', new \DateTime());

        if ($filmTitle) {
            $qb->andWhere('f.title = :filmTitle')
            ->setParameter('filmTitle', $filmTitle);
        }

        if ($date) {
            $startDate = new \DateTime($date);
            $endDate = (clone $startDate)->modify('+1 day');

            $qb->andWhere('s.dateHeureDebut >= :startDate')
            ->andWhere('s.dateHeureDebut < :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);
        }

        return $qb->getQuery()->getResult();
    }
    public function findFutureSeancesByFilm(Film $film): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.film = :film')
            ->andWhere('s.dateHeureDebut >= :now')
            ->setParameter('film', $film)
            ->setParameter('now', new \DateTime())
            ->orderBy('s.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Seance[] Returns an array of Seance objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Seance
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
