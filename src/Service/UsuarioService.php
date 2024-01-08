<?php

namespace App\Service;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;



/**
 * 
 * Servicio para la gestión de usuarios.
 */
class UsuarioService {

    private $usuarioRepository;
    private $validator;
    private $entityManager;

    public function __construct(UsuarioRepository $usuarioRepository, ValidatorInterface $validator,
    EntityManagerInterface $entityManager) {
        $this->usuarioRepository = $usuarioRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }


    /**
     * Crea un nuevo usuario a partir de los datos proporcionados.     
     * @param array $data Datos del usuario: nombre, correo electronico     
     * @throws \InvalidArgumentException Existe un usuario con ese nombre.
     */
    public function crearUsuario(array $data):void
    {    
        $nombre = $data['nombre'];

        //verifico si el usuario ya existe
        $usuarioExistente = $this->usuarioRepository->usuarioExiste($nombre);

        if($usuarioExistente){
            //el usuario ya existe, envío excepcion
            throw new \InvalidArgumentException('Ya existe un usuario con ese nombre');
        }

        //usuario no existe
        $usuario = new Usuario();       
        $usuario->setNombre($nombre);
        $usuario->setCorreoElectronico($data['correo_electronico']);

       // Validamos la entidad usando el validador de Symfony
       $errors = $this->validator->validate($usuario);

       if (count($errors) > 0) {
           // Manejar los errores de validación aquí
           // Puedes lanzar una excepción o manejarlos de otra manera
           throw new \InvalidArgumentException('Correo invalido');
       }


        $this->usuarioRepository->add($usuario,true);           
    } 
    


    /**
     * Actualiza un usuario existente con los datos proporcionados.
     *
     * @param array $data Datos del usuario a actualizar: correo electronico.
     * @throws \InvalidArgumentException Si el usuario no existe o si hay un problema con el formato de los datos.
     */
    public function updateUsuario(array $data): void
    {
        $id = $data['id'];

        //verifico si el usuario ya existe
        $usuarioExistente = $this->usuarioRepository->buscarPorId($id);
        
        if($usuarioExistente){
           //Obtener el usuario existente
            $usuario = $this->usuarioRepository->findOneBy(['id' => $id]);
            
            //Actualizar los datos del usuario
            $usuario->setNombre($data['nombre']);
            $usuario->setCorreoElectronico($data['correo_electronico']);

            // Validamos la entidad usando el validador de Symfony
            $errors = $this->validator->validate($usuario);

            if (count($errors) > 0) {
           // Manejar los errores de validación aquí
           // Puedes lanzar una excepción o manejarlos de otra manera
           throw new \InvalidArgumentException('Correo invalido');
        }

            // Persistir los cambios en la base de datos
            $this->usuarioRepository->add($usuario,true);

        } else{
            throw new \InvalidArgumentException('No existe usuario');
        }       
    }


    /**
     * Lista todos los usuarios disponibles.
     * @return array Lista de usuarios en forma de arrays.
     */
    public function listarUsuarios(): array
    {
           return $this->usuarioRepository->listarTodosUsuarios(); 
    }



      /**
     * Busca un usuario por su número y devuelve sus detalles si se encuentra.
     *
     * @param int $id El ID del usuario a buscar.
     * @return Usuario|null Detalles del usuario si se encuentra, de lo contrario, null.
     */
    public function EncontrarUsuarioPorNumero(int $id): ?Usuario
    {
        //verifico si el usuario ya existe
        $usuarioExistente = $this->usuarioRepository->find($id);
        
        if ($usuarioExistente ) {
            return $usuarioExistente;
        }else{
            return null;
        }        
    }


    /**
     * Elimina un usuario dado su ID.
     *
     * @param int $id El nombre del usuario a eliminar.
     * @return string Un mensaje indicando el resultado de la eliminación.
     */
    public function removeUsuario(int $id): string
    {
        // Busca el usuario por su ID desde el repositorio
        $usuario = $this->usuarioRepository->find($id);

        if (!$usuario) {
            return 'El usuario no existe.';
        }

        // Verifica si el usuario tiene prestamos asociados
        $prestamosAsociados = $usuario->getPrestamos();

        if (!$prestamosAsociados->isEmpty()) {
            return 'El usuario tiene prestamos asociados y no puede ser eliminado';
        }
           
        // Elimina el usuario
        $this->usuarioRepository->remove($usuario, true);

        return 'Usuario eliminado correctamente';
       
    }



}
