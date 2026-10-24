<?php

declare(strict_types=1);

namespace App\Core;

/* #####################################################################
 *  ENRUTADOR (el mismo del 2.7)
 *  Según la URL y el verbo (GET o POST) decide qué método de qué
 *  controlador tiene que ejecutarse.
 * ##################################################################### */

class Router
{
    /* Rutas registradas. Ejemplo:
     * ['GET' => ['/posts' => [PostController::class, 'index']]] */
    private array $routes = [];

    // get() y post() solo apuntan la ruta en $routes. Aún no se ejecuta nada.
    public function get(string $path, array $action): void
    {
        // Ej: $routes['GET']['/posts'] = [PostController::class, 'index']
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, array $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    // Ejecuta la ruta que se ha pedido
    public function dispatch(string $method, string $path): void
    {
        // 1 · Busco la ruta. Si no está registrada, ?? me da null
        $action = $this->routes[$method][$path] ?? null;

        // 2 · Si no existe -> error 404 y termino
        if ($action === null) {
            http_response_code(404);
            echo '404 - Página no encontrada';
            return;
        }

        // 3 · $action es un array de 2 cosas: [NombreDelControlador, 'nombreDelMetodo']
        $controllerClass = $action[0];   // Ej: 'App\Controllers\PostController'
        $methodName = $action[1];        // Ej: 'index'

        /* 4 · Creo el controlador y llamo a su método.
         *     PHP deja usar una variable como nombre de clase o de método.
         *     Por ejemplo, para GET /posts estas dos líneas hacen lo mismo que:
         *         $controller = new PostController();
         *         $controller->index();
         *     (En Java esto solo se podría hacer con reflexión) */
        $controller = new $controllerClass();
        $controller->$methodName();
    }
}
