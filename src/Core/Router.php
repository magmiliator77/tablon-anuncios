<?php

declare(strict_types=1);

namespace App\Core;

/* #####################################################################
 *  ENRUTADOR CON RUTAS LIMPIAS (paso 7)
 *  Según la URL y el verbo (GET o POST) decide qué método de qué
 *  controlador tiene que ejecutarse.
 *
 *  Una ruta puede llevar {id}: un hueco para un número.
 *  Por ejemplo, con la ruta '/posts/delete/{id}':
 *      POST /posts/delete/3  ->  (new PostController())->destroy(3)
 *  Sirve para cualquier controlador y cualquier método: solo hay que
 *  registrar la ruta en public/index.php.
 * ##################################################################### */

class Router
{
    /* Rutas registradas. Ejemplo:
     * ['GET' => ['/posts' => [PostController::class, 'index'],
     *            '/posts/{id}' => [PostController::class, 'show']]] */
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
        // 1 · Las rutas de ese verbo (GET o POST). Si no hay ninguna, un array vacío
        $routes = $this->routes[$method] ?? [];

        // 2 · Pruebo las rutas una a una hasta encontrar la que encaja con la URL
        foreach ($routes as $route => $action) {
            /* Convierto la ruta en una EXPRESIÓN REGULAR (un patrón de texto):
             *   '/posts/delete/{id}'  ->  '#^/posts/delete/(\d+)$#'
             *   (\d+) = "uno o más dígitos", y los paréntesis lo "guardan"
             *   ^ y $ = la URL tiene que ser exactamente eso, ni más ni menos
             *   # es solo el símbolo que marca el principio y el final del patrón */
            $pattern = '#^' . str_replace('{id}', '(\d+)', $route) . '$#';

            /* preg_match() mira si la URL encaja con el patrón.
             * Si encaja, en $matches[1] queda el número que había en el hueco */
            if (preg_match($pattern, $path, $matches)) {
                // 3 · $action es un array de 2 cosas: [NombreDelControlador, 'nombreDelMetodo']
                $controllerClass = $action[0];   // Ej: 'App\Controllers\PostController'
                $methodName = $action[1];        // Ej: 'destroy'

                /* 4 · Creo el controlador y llamo a su método.
                 *     PHP deja usar una variable como nombre de clase o de método.
                 *     Para POST /posts/delete/3 esto hace lo mismo que:
                 *         $controller = new PostController();
                 *         $controller->destroy(3);
                 *     (En Java esto solo se podría hacer con reflexión) */
                $controller = new $controllerClass();

                if (isset($matches[1])) {
                    // La ruta tenía {id}: se lo paso al método como número
                    $controller->$methodName((int) $matches[1]);
                } else {
                    // Ruta sin {id}, como /posts o /login
                    $controller->$methodName();
                }

                return;   // Ya he encontrado la ruta: no sigo buscando
            }
        }

        // 5 · Ninguna ruta encaja -> error 404
        http_response_code(404);
        echo '404 - Página no encontrada';
    }
}
