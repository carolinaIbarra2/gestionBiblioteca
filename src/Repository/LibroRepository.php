<?php

namespace App\Repository;

use App\Entity\Libro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Libro>
 *
 * @method Libro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Libro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Libro[]    findAll()
 * @method Libro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LibroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Libro::class);
    }

    public function add(Libro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Libro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


     /**
     * Verifica si un libro existe según su titulo.
     * @param string $titulo El titulo a verificar.
     * @return Libro|null El libro si existe, de lo contrario, null.
     */
    public function libroExiste($titulo): ?Libro
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.titulo = :titulo')
                ->setParameter('titulo', $titulo)
                ->getQuery()               
                ->getOneOrNullResult()
            ;
        }


    
    /**
     * Obtiene todos los libros almacenados en la base de datos.
     * @return Libro[] Un arreglo con todos los libros almacenados.
     */    
    public function listarTodosLibros(): array
    {
        return $this->createQueryBuilder('u')
       ->getQuery()
        ->getResult();

    }





//    /**
//     * @return Libro[] Returns an array of Libro objects
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

//    public function findOneBySomeField($value): ?Libro
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
