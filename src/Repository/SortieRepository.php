<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Sortie::class);
        $this->security = $security;
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function rechercheFiltre(SearchData $donnees){
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->select('e', 's', 'p')
            ->join('s.etat', 'e')
            ->leftJoin('s.participants', 'p');

        $queryBuilder
            ->andWhere('s.siteOrganisateur = :campus')
            ->setParameter('campus', $donnees->campus);
        $queryBuilder
            ->andWhere('e.libelle != :archiv')
            ->setParameter('archiv', 'Archivée');

        if (!empty($donnees->recherche)) {
            $queryBuilder
                ->andWhere('s.nom LIKE :nom')
                ->setParameter('nom', "%$donnees->recherche%");
        }
        if (!empty($donnees->debutPeriode)) {
            $queryBuilder
                ->andWhere('s.dateHeureDebut > :start')
                ->setParameter('start', $donnees->debutPeriode->format('Y-m-d'));
        }
        if (!empty($donnees->finPeriode)) {
            $queryBuilder
                ->andWhere('s.dateHeureDebut < :end')
                ->setParameter('end', $donnees->finPeriode->format('Y-m-d'));
        }
        if ($donnees->organisateur) {
            $queryBuilder
                ->andWhere('s.organisateur = :org')
                ->setParameter('org', $this->security->getUser()->getId());
        }
        if ($donnees->inscrit) {
            $queryBuilder
                ->andWhere(':insc MEMBER OF s.participants')
                ->setParameter('insc', $this->security->getUser()->getId());
        }
        if ($donnees->pasInscrit) {
            $queryBuilder
                ->andWhere(':notInsc NOT MEMBER OF s.participants')
                ->setParameter('notInsc', $this->security->getUser()->getId());
        }
        if ($donnees->past) {
            $queryBuilder
                ->andWhere('e.libelle = :past')
                ->setParameter('past', 'Passée');
        }else{
            $queryBuilder
                ->andWhere('e.libelle != :past')
                ->setParameter('past', 'Passée');
        }

        return $queryBuilder->getQuery()->getResult();

    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
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

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
