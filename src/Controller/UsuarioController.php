<?php

namespace App\Controller;

use App\Service\UsuarioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Controlador para manejar operaciones relacionadas con usuarios.
 */
class UsuarioController extends AbstractController
{

    private $usuarioService;

    public function __construct(UsuarioService $usuarioService) {
        $this->usuarioService = $usuarioService;
    }

    /**
     * Renderiza la página principal de usuarios cuando se visita la URL /usuario.
     * @return Response La respuesta HTTP que muestra la página de usuarios.
     * @Route("/usuario", name="app_usuario")
     */
    public function index(): Response
    {
        return $this->render('usuario/index.html.twig', [
            'controller_name' => 'UsuarioController',
        ]);
    }


    /**
     * Almacena un usuario en la base de datos.     
     * @param Request $request Contiene los datos enviados por el cliente: nombre, correo electronico
     * @return JsonResponse Mensaje que indica el estado de la función.
     * @Route("/usuario/crear", name="app_usuario_crear", methods={"POST"})
     */
    public function crearUsuario(Request $request)
    {
        try{            
            $data = json_decode($request->getContent(), true);
            $this->usuarioService->crearUsuario($data);
            return $this->json('Usuario guardado');         

        } catch (\InvalidArgumentException $ex) {
            return $this->json($ex->getMessage(), 400);        
        
        }
    }


    /**
     * Actualiza un usuario existente.
     *
     * Este método actualiza los datos de un usuario existente en la base de datos utilizando los datos proporcionados
     * en la solicitud.
     * @param Request $request La solicitud HTTP que contiene los datos del usuario a actualizar.
     * @return JsonResponse Una respuesta JSON que indica el resultado de la actualización.
     * @Route("/usuario/actualizar", methods={"PUT"}, name="app_usuario_actualizar")
     */
    public function updateContrato(Request $request): JsonResponse    
    {                     
        try {
            $data = json_decode($request->getContent(), true);
            $this->usuarioService->updateUsuario($data);
            return $this->json('Usuario actualizado');   
        }catch (\InvalidArgumentException $ex) {
            return $this->json($ex->getMessage(), 400);        
        
        }
    }


     /**
     * Lista todos los usuarios disponibles
     * @return JsonResponse Devuelve un JSON con la lista de usuarios 
     * @Route("/usuario/listar", name="listar_usuarios", methods={"GET"})
     */
    public function listarUsuarios(): JsonResponse
    {
        
        $usuarios = $this->usuarioService->listarUsuarios();
        
        //Convertir los resultados a un formato adecuado para API
        $usuariosArray =[];

        foreach($usuarios as $usuario){
            $usuariosArray[] = [
                'nombre' => $usuario ->getNombre(),
                'correo_electronico' => $usuario ->getCorreoElectronico(),                
                'id_usuario' => $usuario ->getId()
            ];
        }
        return new JsonResponse($usuariosArray);
    }



    /**
     * Busca un usuario por su númeroId y devuelve el usuario.
     * @param int $id El ID del usuario a buscar.
     * @return JsonResponse Devuelve un JSON con los detalles del usuario si se encuentra.
     * @Route("/usuario/{id}/listarUsuario", name="listar_por_usuario", methods={"GET"})
     */
    public function ListarUsuario(int $id): JsonResponse
    {
        $usuario = $this->usuarioService->EncontrarUsuarioPorNumero($id);

        if($usuario ===null){
            return new JsonResponse(['mensaje' => 'El usuario solicitado no existe'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Se obtiene la colección de préstamos
        $prestamos = $usuario->getPrestamos();

        $prestamoArray = [];
        foreach ($prestamos as $prestamo) {
            $prestamoArray[] = [                
            'fecha_inicio' => $prestamo->getFechaInicioFormateada(),
            'fecha_devolucion' => $prestamo->getFechaDevolucionFormateada(),
            'titulo' => $prestamo->getLibros()->getTitulo(), 
         ];
        }

        $usuarioArray = [
            'id_usuario' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'correo_electronico' => $usuario->getCorreoElectronico(),
            'prestamos' => $prestamoArray, // Agrega la información de préstamos al array de usuario
        ];

            return new JsonResponse($usuarioArray);
    }


     /**
     * Elimina un usuario.
     *
     * @param int $id El nombre del usuario a eliminar.
     * @return JsonResponse Devuelve un JSON con el resultado de la operación de eliminación. 
     * @Route("/usuario/{id}/eliminar", name="eliminar_usuario", methods={"DELETE"})
     */
    public function eliminarUsuario(int $id): JsonResponse
    {
        try {
            $mensaje = $this->usuarioService->removeUsuario($id);

            if($mensaje === 'El usuario no existe.'){
                throw new \InvalidArgumentException('El usuario no existe en la base de datos');
            }
            
            return new JsonResponse(['mensaje' => $mensaje]);

        }catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['mensaje' => $exception->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }
    }  
}




