<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Property::class);
    }

    /**
    * @return Query
    */
    public function findAllVisibleQuery(PropertySearch $search) {
        $query = $this->createQueryBuilder('p')->where('p.sold = false');
        if($search->getMaxPrice()) {
            $query = $query
                ->andwhere('p.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }
        if($search->getMinSurface()) {
            $query = $query
                ->andwhere('p.surface >= :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }
        if($search->getOptions()->count() > 0) {
            $o = 0;
            foreach ($search->getOptions() as $option) {
                $o++;
                $query = $query
                    ->andwhere(":option$o MEMBER p.options")
                    ->setParameter("option$o", $option);
            }
        }
        return $query->getQuery();
            //->getResult(); on ne renvoit pas le resultat mais que la requete a cause de la pagination
    }

    /**
    * @return Property[]
    */
    public function findLastest(): array {
        return $this->createQueryBuilder('p')
            ->where('p.sold = false')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value) {
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
    public function findOneBySomeField($value): ?Property {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
