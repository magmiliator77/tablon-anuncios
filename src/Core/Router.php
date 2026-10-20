<?php

declare(strict_types=1);

namespace App\Core;

// #####################################################################
//  ENRUTADOR (el mismo del 2.7)
//  Según la URL y el verbo (GET o POST) decide qué método de qué
//  controlador tiene que ejecutarse.
// #####################################################################

class Router
{
    // Rutas registradas. Ejemplo:
    // ['GET' => ['/posts' => [PostController::class, 'index']]]
    private array $routes = [];

    // get() y post() solo apuntan la ruta en $routes. Aún no se ejecuta nada.
    public function get(string $path, array $action): void
    {
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

        // 3 · Separo [NombreDelControlador, 'nombreDelMetodo']
        [$controllerClass, $methodName] = $action;

        // 4 · Creo el controlador y llamo a su método.
        //     Por ejemplo, para GET /posts esto hace: (new PostController())->index();
        $controller = new $controllerClass();
        $controller->$methodName();
    }
}
