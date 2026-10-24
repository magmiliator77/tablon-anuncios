<?php

declare(strict_types=1);   // Tipado estricto: PHP no convierte tipos "a escondidas"

/* #####################################################################
 *  FRONT CONTROLLER: todas las peticiones entran por aquí
 *  (el .htaccess manda aquí cualquier URL que no sea un archivo real)
 * ##################################################################### */

/* 1 · Cargar las clases. vendor/autoload.php lo genera Composer: cuando
 *     uso App\Core\Router, PHP busca solo src/Core/Router.php */
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\CommentController;

/* 2 · Sesión: a partir de aquí $_SESSION guarda datos entre peticiones
 *     del mismo usuario (quién está logueado, mensajes, token CSRF...) */
session_start();

/* 3 · Token CSRF: un texto secreto aleatorio, UNO por sesión.
 *     Va oculto en todos los formularios POST y se comprueba en
 *     Controller::checkCsrf() */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 4 · Registrar las rutas:  URL  =>  [Controlador, método]
$router = new Router();

// Usuarios
$router->get('/register',  [AuthController::class, 'showRegisterForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login',     [AuthController::class, 'showLoginForm']);
$router->post('/login',    [AuthController::class, 'login']);
$router->get('/logout',    [AuthController::class, 'logout']);

/* Anuncios. Rutas limpias: el id va DENTRO de la URL, en el hueco {id}.
 * Ej: /posts/delete/3 -> el Router llama a destroy(3) */
$router->get('/posts',              [PostController::class, 'index']);    // Listado (+ buscador ?q=)
$router->get('/posts/create',       [PostController::class, 'create']);   // Formulario nuevo
$router->post('/posts',             [PostController::class, 'store']);    // Guardar nuevo
$router->get('/posts/{id}',         [PostController::class, 'show']);     // Ver uno + comentarios
$router->get('/posts/edit/{id}',    [PostController::class, 'edit']);     // Formulario editar
$router->post('/posts/update/{id}', [PostController::class, 'update']);   // Guardar cambios
$router->post('/posts/delete/{id}', [PostController::class, 'destroy']);  // Borrar

// Comentarios
$router->post('/comments', [CommentController::class, 'store']);     // Guardar comentario

/* 5 · Ver qué me han pedido
 *     $path: la ruta sin lo que va detrás de "?"  ("/posts?q=bici" -> "/posts")
 *     $method: 'GET' (abrir un enlace) o 'POST' (enviar un formulario) */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// La portada "/" lleva al listado de anuncios
if ($path === '/') {
    header('Location: /posts');
    exit;
}

// 6 · El Router llama al controlador y método que toque
$router->dispatch($method, $path);
