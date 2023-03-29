<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Playlist>
 *
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    const P_ID_ID = 'p.id id';
    const P_NAME_NAME = 'p.name name';
    const C_NAME_CATEGORIENAME = 'c.name categoriename';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    public function add(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Playlist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Retourne toutes les playlists triées sur un champ
     * @param type $champ
     * @param type $ordre
     * @return Playlist[]
     */
    public function findAllOrderBy($champ, $ordre): array
    {
        return $this->createQueryBuilder('p')
                ->select(self::P_ID_ID)
                ->addSelect(self::P_NAME_NAME)
                ->addSelect(self::C_NAME_CATEGORIENAME)
                ->leftjoin('p.formations', 'f')
                ->leftjoin('f.categories', 'c')
                ->groupBy('p.id')
                ->addGroupBy('c.name')
                ->orderBy('p.'.$champ, $ordre)
                ->addOrderBy('c.name')
                ->getQuery()
                ->getResult();
    }

    /**
     * Enregistrements dont un champ contient une valeur
     * @param type $champ
     * @param type $valeur
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur): array
    {
            return $this->createQueryBuilder('p')
                    ->select(self::P_ID_ID)
                    ->addSelect(self::P_NAME_NAME)
                    ->addSelect(self::C_NAME_CATEGORIENAME)
                    ->leftjoin('p.formations', 'f')
                    ->leftjoin('f.categories', 'c')
                    ->where('p.'.$champ.' LIKE :valeur')
                    ->setParameter('valeur', '%'.$valeur.'%')
                    ->groupBy('p.id')
                    ->addGroupBy('c.name')
                    ->orderBy('p.name', 'ASC')
                    ->addOrderBy('c.name')
                    ->getQuery()
                    ->getResult();
    }


    
}
