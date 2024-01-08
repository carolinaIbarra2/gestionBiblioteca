<?php

namespace App\Controller;

use App\Service\AutorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AutorController extends AbstractController
{
    private $autorService;

    public function __construct(AutorService $autorService) {
        $this->autorService = $autorService;
    }

    /**
     * @Route("/autor", name="app_autor")
     */
    public function index(): Response
    {
        return $this->render('autor/index.html.twig', [
            'controller_name' => 'AutorController',
        ]);
    }

    /**
     * Almacena un autor en la base de datos.     
     * @param Request $request Contiene los datos enviados por el cliente: nombre, fecha nacimiento, biografia
     * @return JsonResponse Mensaje que indica el estado de la función.
     * @Route("/autor/crear", name="app_autor_crear", methods={"POST"})
     */
    public function crearAutor(Request $request)
    {
        try{            
            $data = json_decode($request->getContent(), true);
            $this->autorService->crearAutor($data);
            return $this->json('Autor guardado');         

        } catch (\InvalidArgumentException $ex) {
            return $this->json($ex->getMessage(), 400);        
        
        }
    }


    /**
     * Actualiza un autor existente.
     *
     * Este método actualiza los datos de un autor existente en la base de datos utilizando los datos proporcionados
     * en la solicitud.
     * @param Request $request La solicitud HTTP que contiene los datos del autor a actualizar.
     * @return JsonResponse Una respuesta JSON que indica el resultado de la actualización.
     * @Route("/autor/actualizar", methods={"PUT"}, name="app_autor_actualizar")
     */
    public function updateAutor(Request $request): JsonResponse    
    {                     
        try {
            $data = json_decode($request->getContent(), true);
            $this->autorService->updateAutor($data);
            return $this->json('Autor actualizado');   
        }catch (\InvalidArgumentException $ex) {
            return $this->json($ex->getMessage(), 400);        
        
        }
    }


     /**
     * Lista todos los autores disponibles
     * @return JsonResponse Devuelve un JSON con la lista de autores 
     * @Route("/autor/listar", name="listar_autor", methods={"GET"})
     */
    public function listarAutor(): JsonResponse
    {
        
        $autores = $this->autorService->listarAutores();
        
        //Convertir los resultados a un formato adecuado para API
        $autoresArray =[];

        foreach($autores as $autor){
            $autoresArray[] = [
                'nombre' => $autor ->getNombre(),
                'fecha_nacimiento' => $autor ->getFechaNacimientoFormateada(),  
                'biografia' => $autor ->getBiografia(),               
                'id_autor' => $autor ->getId()
            ];
        }
        return new JsonResponse($autoresArray);
    }



     /**
     * Lista todos los autores disponibles con sus libros
     * @return JsonResponse Devuelve un JSON con la lista de autores 
     * @Route("/autor/listarConLibros", name="listar_autor_libros", methods={"GET"})
     */
    public function listarAutorConLibros(): JsonResponse
    {
        
        $autores = $this->autorService->listarAutores();
        //Convertir los resultados a un formato adecuado para API
        $autoresArray =[];

        foreach($autores as $autor){
            $librosArray = [];
            $libros = $autor->getLibros();
 
            foreach ($libros as $libro) {
                $librosArray[] = [                
                    'titulo' => $libro->getTitulo(),
                    'sinopsis' => $libro->getSinopsis(),
                    'anio_publicacion' => $libro->getAnioPublicacion(), 
                    'cantidad' => $libro->getCantidad(), 
                ];
            }
        
            $autoresArray[] = [
                'nombre' => $autor ->getNombre(),
                'fecha_nacimiento' => $autor ->getFechaNacimientoFormateada(),  
                'biografia' => $autor ->getBiografia(),               
                'id_autor' => $autor ->getId(),
                'libros' => $librosArray
            ];
        }
        return new JsonResponse($autoresArray);
    }



     /**
     * Elimina un autor.
     *
     * @param int $id El nombre del autor a eliminar.
     * @return JsonResponse Devuelve un JSON con el resultado de la operación de eliminación. 
     * @Route("/autor/{id}/eliminar", name="eliminar_autor", methods={"DELETE"})
     */
    public function eliminarAutor(int $id): JsonResponse
    {
        try {
            $mensaje = $this->autorService->removeAutor($id);

            if($mensaje === 'El autor no existe.'){
                throw new \InvalidArgumentException('El autor no existe en la base de datos');
            }
            
            return new JsonResponse(['mensaje' => $mensaje]);

        }catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['mensaje' => $exception->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }
    }  

}
