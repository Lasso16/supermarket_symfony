<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Clase base para todos los controladores de la API.
 * Proporciona métodos comunes para manejar peticiones y respuestas JSON.
 */
abstract class BaseApiController extends AbstractController
{
    /**
     * Extrae el contenido JSON de una petición y lo convierte en un array asociativo.
     *
     * @param Request $request La petición HTTP
     * @return array Los datos decodificados del JSON
     * @throws BadRequestHttpException Si no se reciben datos o el JSON es inválido
     */
    protected function getContent(Request $request): array
    {
        $data = json_decode($request->getContent(), true);

        if (is_null($data)) {
            throw new BadRequestHttpException('No se han recibido los datos');
        }

        return $data;
    }

    /**
     * Genera una respuesta JSON estandarizada.
     * Los datos se envuelven en una clave 'data' por seguridad.
     *
     * @param array|null $data Los datos a incluir en la respuesta
     * @param int $statusCode El código de estado HTTP (por defecto 200 OK)
     * @return JsonResponse La respuesta JSON formateada
     */
    protected function getResponse(?array $data = null, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = new JsonResponse();

        if (!is_null($data)) {
            $result['data'] = $data; // Siempre se debe devolver el resultado dentro de una clave
            $response->setContent(json_encode($result));
        }

        $response->setStatusCode($statusCode);

        return $response;
    }
}
