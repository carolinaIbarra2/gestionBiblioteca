<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Usuario>
 *
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    /**
     * Persiste una entidad usuario en la base de datos.
     * @param Usuario $entity La entidad Usuario a persistir.
     * @param bool $flush Determina si se debe realizar un flush después de persistir la entidad.
     */
    public function add(Usuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Usuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Verifica si un usuario existe según su nombre.
     * @param string $nombre El nombre a verificar.
     * @return Usuario|null El usuario si existe, de lo contrario, null.
     */
    public function usuarioExiste($nombre): ?Usuario
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.nombre = :nombre')
                ->setParameter('nombre', $nombre)
                ->getQuery()               
                ->getOneOrNullResult()
            ;
        }


    /**
     * Verifica si un usuario existe según su id.
     * @param int $id El id a verificar.
     * @return Usuario|null El usuario si existe, de lo contrario, null.
     */
    public function buscarPorId($id): ?Usuario
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()               
            ->getOneOrNullResult()
        ;
    }   


    /**
     * Obtiene todos los usuarios almacenados en la base de datos.
     * @return Usuario[] Un arreglo con todos los usuarios almacenados.
     */    
    public function listarTodosUsuarios(): array
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult()
        ;
    }









//    /**
//     * @return Usuario[] Returns an array of Usuario objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Usuario
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
