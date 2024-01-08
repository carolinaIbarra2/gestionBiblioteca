<?php

namespace App\Service;

use App\Entity\Autor;
use App\Entity\Usuario;
use App\Repository\AutorRepository;

/**
 * 
 * Servicio para la gestión de autores.
 */
class AutorService{

    private $autorRepository;


    public function __construct(AutorRepository $autorRepository) {
        $this->autorRepository = $autorRepository;
    }

    /**
     * Crea un nuevo autor a partir de los datos proporcionados.     
     * @param array $data Datos del autor: nombre,fecha de nacimiento, biografia     
     * @throws \InvalidArgumentException Existe un autor con ese nombre.
     */
    public function crearAutor(array $data):void
    {    
        $nombre = $data['nombre'];

        //verifico si el autor ya existe
        $autorExistente = $this->autorRepository->autorExiste($nombre);

        if($autorExistente){
            //el autor ya existe, envío excepcion
            throw new \InvalidArgumentException('Ya existe un autor con ese nombre');
        }

        //autor no existe
        $autor = new Autor();       
        $autor->setNombre($nombre);

        $fecha = \DateTime::createFromFormat('d-m-Y', $data['fecha_nacimiento']);
            
        if (!$fecha || $fecha->format('d-m-Y') !== $data['fecha_nacimiento']) {
            throw new \InvalidArgumentException('Formato de fecha inválido. Se esperaba el formato d-m-Y.');
        }
            
        $autor->setFechaNacimiento($fecha);
        $autor->setBiografia($data['biografia']);
      

        $this->autorRepository->add($autor,true);           
    } 
    

    /**
     * Actualiza un autor existente con los datos proporcionados.
     *
     * @param array $data Datos del autor a actualizar: nombre, fecha de nacimiento, biografia.
     * @throws \InvalidArgumentException Si el autor no existe o si hay un problema con el formato de los datos.
     */
    public function updateAutor(array $data): void
    {
        $id = $data['id'];

        //verifico si el autor ya existe
        $autor = $this->autorRepository->buscarPorId($id);
        
        if($autor){
            //Actualizar los datos del usuario
            $autor->setNombre($data['nombre']);
           
            $fecha = \DateTime::createFromFormat('d-m-Y', $data['fecha_nacimiento']);
            
            if (!$fecha || $fecha->format('d-m-Y') !== $data['fecha_nacimiento']) {
                throw new \InvalidArgumentException('Formato de fecha inválido. Se esperaba el formato d-m-Y.');
            }
                 
            $autor->setFechaNacimiento($fecha);
            $autor->setBiografia($data['biografia']);

            // Persistir los cambios en la base de datos
            $this->autorRepository->add($autor,true);

        } else{
            throw new \InvalidArgumentException('No existe autor');
        }       
    }


    /**
     * Lista todos los autores disponibles.
     * @return array Lista de autores en forma de arrays.
     */
    public function listarAutores(): array
    {
           return $this->autorRepository->listarTodosAutores(); 
    }


  
    /**
     * Elimina un autor dado su ID.
     *
     * @param int $id El nombre del autor a eliminar.
     * @return string Un mensaje indicando el resultado de la eliminación.
     */
    public function removeAutor(int $id): string
    {
        // Busca el autor por su ID desde el repositorio
        $autor = $this->autorRepository->find($id);

        if (!$autor) {
            return 'El autor no existe.';
        }

        // Verifica si el autor tiene libros asociados
        $librosAsociados = $autor->getLibros();

        if (!$librosAsociados->isEmpty()) {
            return 'El autor tiene libros asociados y no puede ser eliminado';
        }
           
        // Elimina el autor
        $this->autorRepository->remove($autor, true);

        return 'Autor eliminado correctamente';
       
    }



}