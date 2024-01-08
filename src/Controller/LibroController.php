<?php

namespace App\Controller;

use App\Service\LibroServicio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LibroController extends AbstractController
{
    private $libroServicio;

    public function __construct(LibroServicio $libroServicio) {
        $this->libroServicio = $libroServicio;
    }

    /**
     * @Route("/libro", name="app_libro")
     */
    public function index(): Response
    {
        return $this->render('libro/index.html.twig', [
            'controller_name' => 'LibroController',
        ]);
    }


       /**
     * Almacena un libro en la base de datos.     
     * @param Request $request Contiene los datos enviados por el cliente: titulo, sinopsis, año publicacion, cantidad,
     * id autor
     * @return JsonResponse Mensaje que indica el estado de la función.
     * @Route("/libro/crear", name="app_libro_crear", methods={"POST"})
     */
    public function crearLibro(Request $request){

        try{
            $data = json_decode($request->getContent(), true);
            $this->libroServicio->crearLibro($data);
            return $this->json('libro guardado'); 
        }catch (\Exception  $ex) {
            return $this->json($ex->getMessage(), 400);  
        }
    }


    /**
     * Actualiza un libro existente.
     *
     * Este método actualiza los datos de un libro existente en la base de datos utilizando los datos proporcionados
     * en la solicitud.
     * @param Request $request La solicitud HTTP que contiene los datos del libro a actualizar.
     * @return JsonResponse Una respuesta JSON que indica el resultado de la actualización.
     * @Route("/libro/actualizar", methods={"PUT"}, name="app_libro_actualizar")
     */
    public function actualizarLibro(Request $request): JsonResponse
    {
        try{
            $data = json_decode($request->getContent(), true);
            $this->libroServicio->updateLibro($data);
            return $this->json('libro actualizado');
        }catch (\Exception  $ex) {
            return $this->json($ex->getMessage(), 400);  
        }
    }


    /**
     * Lista todos los libros disponibles
     * @return JsonResponse Devuelve un JSON con la lista de libros 
     * @Route("/libro/listar", name="listar_libros", methods={"GET"})
     */
    public function listarTodosLibros()
        {
            //obtener todos los libros
            $libros = $this->libroServicio->listarTodosLibros();
            
            //Convertir los resultados a un formato adecuado para API
            $librosArray =[];
            
            //iterar sobre cada libro y mostrar los datos del libro y su autor
            foreach($libros as $libro){
                //datos del autor del libro
                $autor = $libro->getAutores();
                $categorias = $libro->getCategorias();

                $categoriasArray = [];
                foreach($categorias as $categoria){
                    $categoriasArray[] = [
                        'nombre' => $categoria->getNombre(),
                    ];
                }


                $librosArray[] = [
                    'id_libro' => $libro ->getId(),
                    'titulo' => $libro->getTitulo(),
                    'sinopsis' => $libro->getSinopsis(),
                    'anio_publicacion' => $libro->getAnioPublicacion(),
                    'cantidad' => $libro->getCantidad(),
                    
                    'autor' => [
                        'nombre' => $autor->getNombre(),
                        'fecha_nacimiento' => $autor->getFechaNacimientoFormateada(),
                        'biografia' => $autor->getBiografia()
                    ],
                    'categoria' => $categoriasArray    
                ];

                
            }
            return new JsonResponse($librosArray);

        }


    /**
     * Busca un libro por su númeroId y devuelve el libro.
     * @param int $id El ID del libro a buscar.
     * @return JsonResponse Devuelve un JSON con los detalles del libro si se encuentra.
     * @Route("/libro/{id}/listarLibro", name="listar_por_libro", methods={"GET"})
     */
    public function ListarLibro(int $id): JsonResponse
    {
        $libro = $this->libroServicio->EncontrarLibroPorNumero($id);

        if($libro ===null){
            return new JsonResponse(['mensaje' => 'El libro solicitado no existe'], JsonResponse::HTTP_NOT_FOUND);
        }

        $autor = $libro->getAutores();

        $libroArray = [
            'id_libro' => $libro->getId(),
            'titulo' => $libro->getTitulo(),
            'sinopsis' => $libro->getSinopsis(),
            'anio_publicacion' => $libro->getAnioPublicacion(),
            'cantidad' => $libro->getCantidad(),
            'autor' => [
                'nombre' => $autor->getNombre(),
                'fecha_nacimiento' => $autor->getFechaNacimientoFormateada(),
                'biografia' => $autor->getBiografia()
            ]
        ];

        return new JsonResponse($libroArray);
    }


     /**
     * Lista todos los libros con sus prestamos
     * @return JsonResponse Devuelve un JSON con la lista de libros con prestamos 
     * @Route("/libro/listarConPrestamos", name="listar_autor_prestamos", methods={"GET"})
     */
    public function listarLibroConPrestamos(): JsonResponse
    {
        
        $libros = $this->libroServicio->listarTodosLibros();
        //Convertir los resultados a un formato adecuado para API
        $librosArray =[];

        foreach($libros as $libro){
            $prestamosArray = [];
            $prestamos = $libro->getPrestamos();
 
            foreach ($prestamos as $prestamo) {
                $prestamosArray[] = [                
                    'fecha_inicio' => $prestamo->getFechaInicioFormateada(),
                    'fecha_devolucion' => $prestamo->getFechaDevolucionFormateada(),
                ];
            }
        
            $librosArray[] = [
                'titulo' => $libro ->getTitulo(),
                'sinopsis' => $libro ->getSinopsis(),  
                'anio_publicacion' => $libro ->getAnioPublicacion(),
                'prestamo' => $prestamosArray
            ];
        }
        return new JsonResponse($librosArray);
    }

}


    
