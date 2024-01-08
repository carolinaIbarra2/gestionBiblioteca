<?php

namespace App\Service;
use App\Entity\Prestamo;
use App\Repository\LibroRepository;
use App\Repository\PrestamoRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;


class PrestamoService{

    private $prestamoRepositorio;
    private $usuarioRepositorio;
    private $libroRepositorio;
    private $entityManager;

    public function __construct(PrestamoRepository $prestamoRepositorio, UsuarioRepository $usuarioRepositorio,
    LibroRepository $libroRepositorio, EntityManagerInterface $entityManager) {
        $this->prestamoRepositorio = $prestamoRepositorio;
        $this->usuarioRepositorio = $usuarioRepositorio;
        $this->libroRepositorio = $libroRepositorio;
        $this->entityManager = $entityManager;
    }

    /**
     * Crea un nuevo prestamo a partir de los datos proporcionados.     
     * @param array $data Datos del prestamo: fecha inicio, fecha devolucion, id usuario, id libros.     
     * @throws \InvalidArgumentException Si el formato de fecha es inválido, o si el formato de fecha es invalido.
     */
    public function crearPrestamo(array $data){

        $nombreUsuario = $data["usuario"];
        $nombreTitulo = $data["titulo"];
        
        
        $prestamo = new Prestamo();
        
        $fechaInicial = \DateTime::createFromFormat('d-m-Y', $data['fecha_inicio']);
        $fechaDevolucion = \DateTime::createFromFormat('d-m-Y', $data['fecha_devolucion']);
            
        if (!$fechaInicial || $fechaInicial->format('d-m-Y') !== $data['fecha_inicio'] ||
        !$fechaDevolucion || $fechaDevolucion->format('d-m-Y') !== $data['fecha_devolucion'] ) {
            throw new \InvalidArgumentException('Formato de fecha inválido. Se esperaba el formato d-m-Y.');
        }
            
        $prestamo->setFechaInicio($fechaInicial);
        $prestamo->setFechaDevolucion($fechaDevolucion);

        //verificar si el usuario existe
        $usuarioExistente = $this->usuarioRepositorio->findOneBy(['nombre' => $nombreUsuario]);

        if (!$usuarioExistente) {
            throw new \InvalidArgumentException('El usuario no existe.');
        }

        //verificar si el libro existe
        $libroExistente = $this->libroRepositorio->findOneBy(['titulo' => $nombreTitulo]);

        if (!$libroExistente) {
            throw new \InvalidArgumentException('El libro no existe.');
        }
       
        //Agregar el prestamo al usuario y libro Existente
        $usuarioExistente->addPrestamo($prestamo);
        $libroExistente->addPrestamo($prestamo);

        //persistir los cambios
        $this->entityManager->persist($prestamo);
        $this->entityManager->flush();      
    }


    /**
     * Actualiza un prestamo existente con los datos proporcionados.
     *
     * @param array $data Datos del prestamo a actualizar: fecha inicio y devolucion
     * @throws \InvalidArgumentException Si el prestamo no existe o si hay un problema con el formato de los datos.
     */
    public function updatePrestamo(array $data): void
    {
        $id = $data['id'];

        //verifico si el prestamo ya existe
        $prestamoExistente = $this->prestamoRepositorio->find($id);
        
        if($prestamoExistente){
           //Obtener el prestamo existente
            $prestamo = $this->prestamoRepositorio->findOneBy(['id' => $id]);
            
            //Actualizar los datos del prestamo
            $fechaInicial = \DateTime::createFromFormat('d-m-Y', $data['fecha_inicio']);
            $fechaDevolucion = \DateTime::createFromFormat('d-m-Y', $data['fecha_devolucion']);
                
            if (!$fechaInicial || $fechaInicial->format('d-m-Y') !== $data['fecha_inicio'] ||
            !$fechaDevolucion || $fechaDevolucion->format('d-m-Y') !== $data['fecha_devolucion'] ) {
                throw new \InvalidArgumentException('Formato de fecha inválido. Se esperaba el formato d-m-Y.');
            }
                
            $prestamo->setFechaInicio($fechaInicial);
            $prestamo->setFechaDevolucion($fechaDevolucion);
            
            // Persistir los cambios en la base de datos
            $this->entityManager->flush();
            } else{
                throw new \InvalidArgumentException('No existe el prestamo');
            }    

        }
        
        /**
         * Lista todos los prestamos disponibles.
         * @return array Lista de prestamos en forma de arrays.
         */
        public function listarTodosPrestamos()
        {
           return $this->prestamoRepositorio->findAll();
        }


    /**
     * Elimina un prestamo dado su ID.
     *
     * @param int $id El id del prestamo a eliminar.
     * @return string Un mensaje indicando el resultado de la eliminación.
     */
    public function removePrestamo(int $id): string
    {
        // Busca el prestamo por su ID desde el repositorio
        $prestamo = $this->prestamoRepositorio->find($id);

        if (!$prestamo) {
            return 'El prestamo no existe.';
        }
           
        // Elimina el prestamo
        $this->prestamoRepositorio->remove($prestamo, true);

        return 'Prestamo eliminado correctamente';
       
    }

}