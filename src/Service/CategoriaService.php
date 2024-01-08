<?php

namespace App\Service;
use App\Entity\Categoria;
use App\Repository\CategoriaRepository;

/**
 * 
 * Servicio para la gestión de categorias, incluyendo creación, eliminación, actualización y visualizacion.
 */
class CategoriaService{

    private $categoriaRepositorio;

    public function __construct(CategoriaRepository $categoriaRepositorio) {
        $this->categoriaRepositorio = $categoriaRepositorio;
    }


    /**
     * Crea una nueva categoria a partir de los datos proporcionados.     
     * @param array $data Datos del grupo: nombre, descripcion.     
     * @throws \InvalidArgumentException ya existe un grupo con ese nombre.
     */
    public function crearCategoria(array $data){

        $nombre = $data['nombre'];

        //verifico si la categoria existe
        $categoriaExistente = $this->categoriaRepositorio->categoriaExiste($nombre);
        if($categoriaExistente){
            //la categoria ya existe, envío excepcion
            throw new \InvalidArgumentException('Ya existe una categoria con ese nombre');      
        }

        //categoria no existe
        $categoria = new Categoria();
        $categoria->setNombre($nombre);
        $categoria->setDescripcion($data['descripcion']);

        $this->categoriaRepositorio->add($categoria,true);
    }


    /**
     * Actualiza una categoria existente con los datos proporcionados.
     *
     * @param array $data Datos de la categoria a actualizar: nombre, descripcion
     * @throws \InvalidArgumentException Si la categoria no existe.
     */
    public function updateCategoria(array $data): void
    {
        $id = $data['id'];

        //verifico si la categoria ya existe
        $categoria = $this->categoriaRepositorio->find($id);
        
        if($categoria){
            //Actualizar los datos de la categoria            
            $categoria->setDescripcion($data['descripcion']);

            // Persistir los cambios en la base de datos
            $this->categoriaRepositorio->add($categoria,true);

        } else{
            throw new \InvalidArgumentException('No existe categoria');
        }       
    }   


    /**
     * Lista todas las categorias disponibles.
     * @return array Lista de categorias en forma de arrays.
     */
    public function listarCategorias(): array
    {
        return $this->categoriaRepositorio->listarTodosCategorias();
    }





    /**
     * Elimina una categoria dado su ID.
     *
     * @param int $id El id de la categoria a eliminar.
     * @return string Un mensaje indicando el resultado de la eliminación.
     */
    public function removeCategoria(int $id): string
    {
        // Busca la categoria por su ID desde el repositorio
        $categoria = $this->categoriaRepositorio->find($id);

        if (!$categoria) {
            return 'La categoria no existe.';
        }

        // Verifica si la categoria tiene libros asociados
        $librosAsociados = $categoria->getLibros();

        if (!$librosAsociados->isEmpty()) {
            return 'La categoria tiene libros asociados y no puede ser eliminado';
        }

        // Se itera sobre los libros asociados para eliminar la categoria
        foreach ($librosAsociados as $librosAsociado) {
            $librosAsociado->removeCategoria($categoria);
        }
    
        // Si la categoria está vacía, procede a eliminarla
        $this->categoriaRepositorio->remove($categoria, true);

        return 'Categoria eliminada correctamente';
       
    }




}