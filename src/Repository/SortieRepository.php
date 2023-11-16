<?php

namespace App\Repository;

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

    public function rechercheFiltre($donnees){
        if (!empty($donnees)){
            $queryBuilder = $this
                ->createQueryBuilder('s')
                ->select('e', 's')
                ->join('s.etat', 'e')
                ->leftJoin('s.participants', 'p');

            if ($donnees['campus']) {
                $queryBuilder = $queryBuilder
                    ->andWhere('s.siteOrganisateur = :campus')
                    ->setParameter('campus', $donnees['campus']->getId());
            }
            if ($donnees['nom']) {
                $nom = $donnees['nom'];
                $queryBuilder = $queryBuilder
                    ->andWhere('s.nom LIKE :nom')
                    ->setParameter('nom', "%$nom%");
            }
            if ($donnees['debut_periode']) {
                $queryBuilder = $queryBuilder
                    ->andWhere('s.dateHeureDebut > :start')
                    ->setParameter('start', $donnees['debut_periode']->format('Y-m-d'));
            }
            if ($donnees['fin_periode']) {
                $queryBuilder = $queryBuilder
                    ->andWhere('s.dateHeureDebut < :end')
                    ->setParameter('end', $donnees['fin_periode']->format('Y-m-d'));
            }
            if ($donnees['organisateur']) {
                $queryBuilder = $queryBuilder
                    ->andWhere('s.organisateur = :org')
                    ->setParameter('org', $this->security->getUser()->getId());
            }
            if ($donnees['inscrit']) {
                $queryBuilder = $queryBuilder
                    ->andWhere(':insc MEMBER OF s.participants')
                    ->setParameter('insc', $this->security->getUser()->getId());
            }
            if ($donnees['pasInscrit']) {
                $queryBuilder = $queryBuilder
                    ->andWhere(':notInsc NOT MEMBER OF s.participants')
                    ->setParameter('notInsc', $this->security->getUser()->getId());
            }
            if ($donnees['past']) {
                $queryBuilder = $queryBuilder
                    ->andWhere('e.id = :past')
                    ->setParameter('past', '3');
            }

            return $queryBuilder->getQuery()->getResult();
        }else{
           return $this->findAll();
        }
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
