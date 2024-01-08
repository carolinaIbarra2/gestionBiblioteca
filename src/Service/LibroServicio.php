<?php

namespace App\Service;
use App\Entity\Libro;
use App\Repository\AutorRepository;
use App\Repository\CategoriaRepository;
use App\Repository\LibroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * 
 * Servicio para la gestión de libros, incluyendo creación, eliminación, actualización y visualizacion.
 */
class LibroServicio {

private $libroRepository;
private $autorRepository;
private $entityManager;
private $validator;
private $serializer;
private $categoriaRepository;

public function __construct(LibroRepository $libroRepository, AutorRepository $autorRepository,
EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer,
CategoriaRepository $categoriaRepository) {
    $this->libroRepository = $libroRepository;
    $this->autorRepository = $autorRepository;
    $this->entityManager = $entityManager;
    $this->validator = $validator;
    $this->serializer = $serializer;
    $this->categoriaRepository = $categoriaRepository;
}


public function crearLibro(array $data){

    $titulo = $data['titulo'];
    $idAutor = $data['id_autor'];
    $nombreCategoria = $data['categoria'];

    //verifico si el libro existe
    $libroExistente = $this->libroRepository->libroExiste($titulo);

    if($libroExistente){
        //el libro ya existe, envío excepcion
        throw new \InvalidArgumentException('Ya existe un libro con ese titulo');       
    }

    //libro no existe
    $libro = new Libro();
    $libro->setTitulo($titulo);

    $libro->setSinopsis($data['sinopsis']);
    
    $libro->setAnioPublicacion($data['anio_publicacion']);

    $libro->setCantidad($data['cantidad']);

    //verificar si el autor existe
    $autorExistente = $this->autorRepository->findOneBy(['id' => $idAutor]);

    if(!$autorExistente){
        throw new \InvalidArgumentException('El autor no existe.');
    }

    //verificar si la categoria existe
    $categoriaExistente = $this->categoriaRepository->findOneBy(['nombre' => $nombreCategoria]);

    if(!$categoriaExistente){
        throw new \InvalidArgumentException('La categoria no existe.');
    }


    // Validamos la entidad usando el validador de Symfony
    $errors = $this->validator->validate($libro);

    if (count($errors) > 0) {
        // Manejar los errores de validación aquí
        // Puedes lanzar una excepción o manejarlos de otra manera
        throw new \InvalidArgumentException('alguno(s) de los datos se ingresaron mal');
    }

    //Agregar el libro al autor Existente
    $autorExistente->addLibro($libro);

    //Agregar el libro a la categoria existente
    $categoriaExistente->addLibro($libro);

    //persistir los cambios
    $this->entityManager->persist($libro);
        $this->entityManager->flush();
}


 /**
     * Actualiza un libro existente con los datos proporcionados.
     *
     * @param array $data Datos del libro a actualizar: cantidad, id autor.
     * @throws \InvalidArgumentException Si el libro no existe o si hay un problema con el formato de los datos.
     */
    public function updateLibro(array $data): void
    {
        $id = $data['id'];

        //verifico si el libro ya existe
        $libroExistente = $this->libroRepository->find($id);
        
        if($libroExistente){
           //Obtener el libro existente
            $libro = $this->libroRepository->findOneBy(['id' => $id]);
            
            //Actualizar los datos del libro
            $libro->setCantidad($data['cantidad']);

            //verificar si el autor existe
            $idAutor = $data['id_autor'];
            $autorExistente = $this->autorRepository->find(['id' => $idAutor]);
        
            if(!$autorExistente){
                throw new \InvalidArgumentException('El autor no existe.');
            }

             // Asignar el autor al libro
            $libro->setAutores($autorExistente);
        
            // Validamos la entidad usando el validador de Symfony
            $errors = $this->validator->validate($libro);
        
            if (count($errors) > 0) {
                // Manejar los errores de validación aquí
                // Puedes lanzar una excepción o manejarlos de otra manera
                throw new \InvalidArgumentException('alguno(s) de los datos se ingresaron mal');
            }
        
            // Persistir los cambios en la base de datos
            $this->entityManager->flush();
            } else{
                throw new \InvalidArgumentException('No existe el libro');
            }    

    }


        /**
         * Lista todos los libros disponibles.
         * @return array Lista de libros en forma de arrays.
         */
        public function listarTodosLibros()
        {
           return $this->libroRepository->listarTodosLibros();
        }



    /**
     * Busca un libro por su número y devuelve sus detalles si se encuentra.
     *
     * @param int $id El ID del libro a buscar.
     * @return Libro|null Detalles del libro si se encuentra, de lo contrario, null.
     */
    public function EncontrarLibroPorNumero(int $id): ?Libro
    {
        //verifico si el libro ya existe
        $libroExistente = $this->libroRepository->find($id);
        
        if ($libroExistente ) {
            return $libroExistente;
        }else{
            return null;
        }

        
        
    }
}
