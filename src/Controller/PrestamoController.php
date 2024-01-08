<?php

namespace App\Controller;

use App\Service\PrestamoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class PrestamoController extends AbstractController
{
    private $prestamoService;

    public function __construct(PrestamoService $prestamoService) {
        $this->prestamoService = $prestamoService;
    }


    /**
     * @Route("/prestamo", name="app_prestamo")
     */
    public function index(): Response
    {
        return $this->render('prestamo/index.html.twig', [
            'controller_name' => 'PrestamoController',
        ]);
    }


    /**
     * Almacena un prestamo en la base de datos.     
     * @param Request $request Contiene los datos enviados por el cliente: fecha inicio, fecha devolucion, 
     * id usuario, id libro
     * @return JsonResponse Mensaje que indica el estado de la función.
     * @Route("/prestamo/crear", name="app_prestamo_crear", methods={"POST"})
     */
    public function crearPrestamo(Request $request){

        try{
            $data = json_decode($request->getContent(), true);
            $this->prestamoService->crearPrestamo($data);
            return $this->json('prestamo guardado'); 
        }catch (\Exception  $ex) {
            return $this->json($ex->getMessage(), 400);  
        }
    }


     /**
     * Actualiza un prestamo existente.
     *
     * Este método actualiza los datos de un prestamo existente en la base de datos utilizando los datos proporcionados
     * en la solicitud.
     * @param Request $request La solicitud HTTP que contiene los datos del prestamo a actualizar.
     * @return JsonResponse Una respuesta JSON que indica el resultado de la actualización.
     * @Route("/prestamo/actualizar", methods={"PUT"}, name="app_prestamo_actualizar")
     */
    public function actualizarLibro(Request $request): JsonResponse
    {
        try{
            $data = json_decode($request->getContent(), true);
            $this->prestamoService->updatePrestamo($data);
            return $this->json('prestamo actualizado');
        }catch (\Exception  $ex) {
            return $this->json($ex->getMessage(), 400);  
        }
    }

   
    /**
     * Lista todos los prestamos disponibles
     * @return JsonResponse Devuelve un JSON con la lista de prestamos 
     * @Route("/prestamo/listar", name="listar_prestamo", methods={"GET"})
     */
    public function listarTodosPrestamos()
        {
            //obtener todos los prestamos
            $prestamos = $this->prestamoService->listarTodosPrestamos();
            
            //Convertir los resultados a un formato adecuado para API
            $prestamosArray =[];
            
            //iterar sobre cada prestamo y mostrar los datos del prestamo, libro, usuario
            foreach($prestamos as $prestamo){
                
                //datos del libro del prestamo
                $libro = $prestamo->getLibros();
                //datos del usuario del prestamo
                $usuario = $prestamo->getUsuarios();

                $prestamosArray[] = [
                    'id_prestamo' => $prestamo ->getId(),
                    'fecha_inicio' => $prestamo->getFechaInicioFormateada(),
                    'fecha_devolucion' => $prestamo->getFechaDevolucionFormateada(),
                    'anio_publicacion' => $libro->getAnioPublicacion(),
                    'libro' => [
                        'titulo' => $libro->getTitulo(),
                    ],
                    'usuario' =>[
                        'nombre' => $usuario->getNombre(),
                        'correo_electronico' => $usuario->getCorreoElectronico()
                    ]                    
                ];

                
            }
            return new JsonResponse($prestamosArray);

    }


     /**
     * Elimina un prestamo.
     *
     * @param int $id El id del prestamo a eliminar.
     * @return JsonResponse Devuelve un JSON con el resultado de la operación de eliminación. 
     * @Route("/prestamo/{id}/eliminar", name="eliminar_prestamo", methods={"DELETE"})
     */
    public function eliminarPrestamo(int $id): JsonResponse
    {
        try {
            $mensaje = $this->prestamoService->removePrestamo($id);

            if($mensaje === 'El prestamo no existe.'){
                throw new \InvalidArgumentException('El prestamo no existe en la base de datos');
            }
            
            return new JsonResponse(['mensaje' => $mensaje]);

        }catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['mensaje' => $exception->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }
    }  

}
