<?php

namespace App\Controller\API;

use App\BLL\ProductoBLL;
use App\Entity\Producto;
use App\Repository\CategoriaRepository;
use App\Repository\UsuarioRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class ProductoApiController extends BaseApiController
{
    private ProductoBLL $productoBLL;

    public function __construct(ProductoBLL $productoBLL)
    {
        $this->productoBLL = $productoBLL;
    }

    #[Route('/prueba', name: 'api_prueba', methods: ["GET"])]
    public function pruebaApi(): JsonResponse
    {
        return $this->json([
            'message' => 'Bienvenido al controlador de la API de Productos!',
        ]);
    }

    /**
     * Crear un nuevo producto vía API
     * 
     * POST /api/producto
     * Body JSON: {
     *   "titulo": "Nombre del producto",
     *   "descripcion": "Descripción opcional",
     *   "precio": 19.99,
     *   "categoria_id": 1,
     *   "usuario_id": 1
     * }
     */
    #[Route('/producto', name: 'api_producto_create', methods: ["POST"])]
    public function create(
        Request $request,
        // EntityManagerInterface $entityManager, // Removed
        CategoriaRepository $categoriaRepository, // Mantenemos para buscar categoria por ahora, idealmente en BLL también
        UsuarioRepository $usuarioRepository    // Mantenemos para buscar usuario
    ): JsonResponse {
        try {
            $data = $this->getContent($request);

            // Validar campos requeridos
            if (empty($data['titulo'])) {
                return $this->getResponse(
                    ['error' => 'El título es obligatorio'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Validación de usuario delegado al BLL (o vía Security)
            // Si la ruta está protegida por JWT, getUser() devolverá el usuario.
            // Si no hay usuario, el BLL no asignará nada y la BD lanzará error si el campo es obligatorio.
            if (!$this->getUser()) {
                return $this->getResponse(['error' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
            }
            // Eliminamos la asignación manual aquí, el BLL lo hará.

            // Crear producto
            $producto = new Producto();
            $producto->setTitulo($data['titulo']);
            // Usuario asignado en BLL::create()

            // Campos opcionales
            if (!empty($data['descripcion'])) {
                $producto->setDescripcion($data['descripcion']);
            }

            if (!empty($data['precio'])) {
                $producto->setPrecio($data['precio']);
            }

            if (!empty($data['categoria_id'])) {
                $categoria = $categoriaRepository->find($data['categoria_id']);
                if ($categoria) {
                    $producto->setCategoria($categoria);
                }
            }

            if (!empty($data['fecha_inicial'])) {
                $producto->setFechaInicial(new \DateTime($data['fecha_inicial']));
            }

            if (!empty($data['fecha_final'])) {
                $producto->setFechaFinal(new \DateTime($data['fecha_final']));
            }

            // $entityManager->persist($producto); // Replaced
            // $entityManager->flush(); // Replaced
            // $entityManager->persist($producto); // Replaced
            // $entityManager->flush(); // Replaced
            $this->productoBLL->create($producto, $data); // Added support for data/image processing

            return $this->getResponse([
                'id' => $producto->getId(),
                'titulo' => $producto->getTitulo(),
                'descripcion' => $producto->getDescripcion(),
                'precio' => $producto->getPrecio(),
                'mensaje' => 'Producto creado correctamente'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->getResponse(
                ['error' => 'Error: ' . $e->getMessage()], // Modified
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Obtener todos los productos con filtros opcionales
     * GET /api/productos?q=tomate&categoria=1&fecha_desde=2024-01-01
     */
    #[Route('/productos', name: 'api_producto_index', methods: ["GET"])]
    public function index(Request $request): JsonResponse // Modified
    {
        $filters = [];

        if ($request->query->has('q')) {
            $filters['search'] = $request->query->get('q');
        }

        if ($request->query->has('categoria')) {
            $filters['categoria'] = $request->query->get('categoria');
        }

        if ($request->query->has('fecha_desde')) {
            $filters['fecha_desde'] = $request->query->get('fecha_desde');
        }

        if ($request->query->has('fecha_hasta')) {
            $filters['fecha_hasta'] = $request->query->get('fecha_hasta');
        }

        // Si hay filtros usamos findAllFiltered, si no, findAll (o findAllFiltered con array vacío)
        // $productos = $this->productoBLL->getFilteredAll($filters); // Replaced
        $productos = $this->productoBLL->getAll($filters, true); // Added security enforcement

        $data = [];
        foreach ($productos as $producto) {
            $data[] = $this->serializeProducto($producto);
        }
        // $data = array_map(fn($p) => $this->serializeProducto($p), $productos); // Alternative as per instruction

        return $this->getResponse($data);
    }

    /**
     * Obtener un producto por ID
     * GET /api/producto/{id}
     */
    #[Route('/producto/{id}', name: 'api_producto_show', methods: ["GET"])]
    public function show(int $id): JsonResponse // Modified
    {
        // $producto = $productoRepository->find($id); // Replaced
        $producto = $this->productoBLL->getById($id); // Added

        if (!$producto) {
            return $this->getResponse(
                ['error' => 'Producto no encontrado'],
                Response::HTTP_NOT_FOUND
            );
        }

        // Verificar acceso (Admin o Propietario)
        try {
            $this->productoBLL->checkAccessToProducto($producto);
        } catch (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            return $this->getResponse(['error' => 'Acceso denegado'], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return $this->getResponse(['error' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        }

        return $this->getResponse($this->serializeProducto($producto));
    }

    /**
     * Actualizar un producto
     * PUT /api/producto/{id}
     */
    #[Route('/producto/{id}', name: 'api_producto_update', methods: ["PUT"])]
    public function update(
        int $id,
        Request $request,
        // ProductoRepository $productoRepository, // Removed
        CategoriaRepository $categoriaRepository // Kept
        // EntityManagerInterface $entityManager // Removed
    ): JsonResponse {
        try {
            // $producto = $productoRepository->find($id); // Replaced
            $producto = $this->productoBLL->getById($id); // Added

            if (!$producto) {
                return $this->getResponse(
                    ['error' => 'Producto no encontrado'],
                    Response::HTTP_NOT_FOUND
                );
            }

            $data = $this->getContent($request);

            // Actualizar campos si se proporcionan
            if (isset($data['titulo'])) {
                $producto->setTitulo($data['titulo']);
            }

            if (isset($data['descripcion'])) {
                $producto->setDescripcion($data['descripcion']);
            }

            if (isset($data['precio'])) {
                $producto->setPrecio($data['precio']);
            }

            if (isset($data['categoria_id'])) {
                $categoria = $categoriaRepository->find($data['categoria_id']);
                if ($categoria) {
                    $producto->setCategoria($categoria);
                }
            }

            if (isset($data['fecha_inicial'])) {
                $producto->setFechaInicial(new \DateTime($data['fecha_inicial']));
            }

            if (isset($data['fecha_final'])) {
                $producto->setFechaFinal(new \DateTime($data['fecha_final']));
            }

            // $entityManager->flush(); // Replaced
            // $entityManager->flush(); // Replaced
            $this->productoBLL->update($producto, $data); // Added support for data/image processing

            return $this->getResponse([
                'mensaje' => 'Producto actualizado correctamente',
                'producto' => $this->serializeProducto($producto)
            ]);
        } catch (\Exception $e) {
            return $this->getResponse(
                ['error' => 'Error: ' . $e->getMessage()], // Modified
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Eliminar un producto
     * DELETE /api/producto/{id}
     */
    #[Route('/producto/{id}', name: 'api_producto_delete', methods: ["DELETE"])]
    public function delete(
        int $id
        // ProductoRepository $productoRepository, // Removed
        // EntityManagerInterface $entityManager // Removed
    ): JsonResponse {
        // $producto = $productoRepository->find($id); // Replaced
        $producto = $this->productoBLL->getById($id); // Added

        if (!$producto) {
            return $this->getResponse(
                ['error' => 'Producto no encontrado'],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            // Nota: En una API real, deberíamos obtener el usuario autenticado del token
            // Como ejemplo, aquí asumimos que el BLL manejará la lógica, pero necesitamos pasarle un usuario.
            // Si la API es pública/sin auth real aun, esto fallará en el BLL si espera un usuario válido.
            // Para mantener la consistencia con el ejemplo, intentaremos obtener el usuario si existe, o simular uno si es prueba.
            // IMPORTANTE: El método delete del BLL requiere un UserInterface. 
            // Si no estamos autenticados, esto lanzará error.

            $user = $this->getUser();
            if (!$user) {
                return $this->getResponse(['error' => 'Debes estar autenticado para eliminar'], Response::HTTP_UNAUTHORIZED);
            }

            // $entityManager->remove($producto); // Replaced
            // $entityManager->flush(); // Replaced
            $this->productoBLL->delete($producto, $user); // Added

            return $this->getResponse([
                'mensaje' => 'Producto eliminado correctamente',
                'id' => $id
            ]);
        } catch (\Exception $e) {
            return $this->getResponse(['error' => $e->getMessage()], Response::HTTP_FORBIDDEN); // Modified
        }
    }

    /**
     * Método auxiliar para serializar un producto a array
     */
    private function serializeProducto(Producto $producto): array
    {
        return [
            'id' => $producto->getId(),
            'titulo' => $producto->getTitulo(),
            'descripcion' => $producto->getDescripcion(),
            'precio' => $producto->getPrecio(),
            'imagen' => $producto->getImagen(),
            'categoria' => $producto->getCategoria() ? [
                'id' => $producto->getCategoria()->getId(),
                'nombre' => $producto->getCategoria()->getNombre()
            ] : null,
            'usuario' => [
                'id' => $producto->getUsuario()->getId(),
                'nombre' => $producto->getUsuario()->getNombreUsuario()
            ],
            'fecha_creacion' => $producto->getFechaCreacion()?->format('Y-m-d H:i:s'),
            'fecha_inicial' => $producto->getFechaInicial()?->format('Y-m-d'),
            'fecha_final' => $producto->getFechaFinal()?->format('Y-m-d'),
        ];
    }
}
