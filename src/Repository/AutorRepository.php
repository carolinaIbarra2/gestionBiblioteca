<?php

namespace App\Repository;

use App\Entity\Autor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Autor>
 *
 * @method Autor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Autor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Autor[]    findAll()
 * @method Autor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Autor::class);
    }


    /**
     * Persiste una entidad autor en la base de datos.
     * @param Autor $entity La entidad Autor a persistir.
     * @param bool $flush Determina si se debe realizar un flush después de persistir la entidad.
     */
    public function add(Autor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Autor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Verifica si un autor existe según su id.
     * @param string $nombre El nombre a verificar.
     * @return Autor|null El autor si existe, de lo contrario, null.
     */
    public function autorExiste($nombre): ?Autor
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.nombre = :nombre')
                ->setParameter('nombre', $nombre)
                ->getQuery()               
                ->getOneOrNullResult()
            ;
        }

   
    /**
     * Verifica si un autor existe según su id.
     * @param int $id El id a verificar.
     * @return Autor|null El autor si existe, de lo contrario, null.
     */
    public function buscarPorId($id): ?Autor
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()               
            ->getOneOrNullResult()
        ;
    }  


    /**
     * Obtiene todos los autores almacenados en la base de datos.
     * @return Autor[] Un arreglo con todos los autores almacenados.
     */    
    public function listarTodosAutores(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult()
        ;
    }


//    /**
//     * @return Autor[] Returns an array of Autor objects
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

//    public function findOneBySomeField($value): ?Autor
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
