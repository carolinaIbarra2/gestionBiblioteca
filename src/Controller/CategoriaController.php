<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\CategoriaService;

class CategoriaController extends AbstractController
{
    private $categoriaServicio;

    public function __construct(CategoriaService $categoriaServicio) {
        $this->categoriaServicio = $categoriaServicio;
    }


    /**
     * @Route("/categoria", name="app_categoria")
     */
    public function index(): Response
    {
        return $this->render('categoria/index.html.twig', [
            'controller_name' => 'CategoriaController',
        ]);
    }


    /**
     * Almacena una categoria en la base de datos.     
     * @param Request $request Contiene los datos enviados por el cliente: nombre y descripcion
     * @return JsonResponse Mensaje que indica el estado de la función.
     * @Route("/categoria/crear", name="app_categoria_crear", methods={"POST"})
     */
    public function crearGrupo(Request $request)
    {
        try{
            $data = json_decode($request->getContent(), true);
            $this->categoriaServicio->crearCategoria($data);
            return $this->json('Categoria creada'); 
        }catch (\Exception  $ex) {
            return $this->json($ex->getMessage(), 400);  
        }

    }



    /**
     * Actualiza una categoria existente.
     *
     * Este método actualiza los datos de una categoria existente en la base de datos utilizando los datos proporcionados
     * en la solicitud.
     * @param Request $request La solicitud HTTP que contiene los datos de la categoria a actualizar.
     * @return JsonResponse Una respuesta JSON que indica el resultado de la actualización.
     * @Route("/categoria/actualizar", methods={"PUT"}, name="app_categoria_actualizar")
     */
    public function updateCategoria(Request $request): JsonResponse    
    {                     
        try {
            $data = json_decode($request->getContent(), true);
            $this->categoriaServicio->updateCategoria($data);
            return $this->json('Categoria actualizada');   
        }catch (\InvalidArgumentException $ex) {
            return $this->json($ex->getMessage(), 400);        
        
        }
    }


    /**
     * Lista todas las categorias disponibles
     * @return JsonResponse Devuelve un JSON con la lista de categorias 
     * @Route("/categorias/listar", name="listar_categorias", methods={"GET"})
     */
    public function listarCategorias(): JsonResponse
    {
        $categorias = $this->categoriaServicio->listarCategorias();

        //Convertir los resultados a un formato adecuado para API
        $categoriasArray =[];

        foreach($categorias as $categoria){
            $categoriasArray[] = [
                'nombre' => $categoria->getNombre(),
                'descripcion' => $categoria->getDescripcion(),
                'id_categoria' => $categoria->getId(),
            ];            
        }
        return new JsonResponse($categoriasArray);
    }



    /**
     * Elimina una categoria.
     *
     * @param int $id El id de la categoria a eliminar.
     * @return JsonResponse Devuelve un JSON con el resultado de la operación de eliminación. 
     * @Route("/categoria/{id}/eliminar", name="eliminar_categoria", methods={"DELETE"})
     */
    public function eliminarCategoria(int $id): JsonResponse
    {
        try {
            $mensaje = $this->categoriaServicio->removeCategoria($id);

            if($mensaje === 'La categoria no existe.'){
                throw new \InvalidArgumentException('La categoria no existe en la base de datos');
            }
            
            return new JsonResponse(['mensaje' => $mensaje]);

        }catch (\InvalidArgumentException $exception) {
            return new JsonResponse(['mensaje' => $exception->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }
    }  

}
