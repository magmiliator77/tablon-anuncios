<?php

declare(strict_types=1);

namespace App\Core;

/* #####################################################################
 *  CONTROLADOR BASE
 *  Todos los controladores heredan de aquí. Guarda lo que se repite en
 *  todos ellos, para no copiarlo tres veces:
 *   - la conexión a la BD
 *   - comprobar el token CSRF
 *   - redirigir con un mensaje
 *   - obligar a estar logueado
 *   - mostrar una página de error (404, 403)
 * ##################################################################### */

abstract class Controller
{
    protected Database $db;

    /* Una sola conexión por petición: cada petición crea un único
     * controlador, y ese controlador crea una única Database */
    public function __construct()
    {
        $this->db = new Database();
    }

    /* =================================================================
     *  checkCsrf(): se llama AL PRINCIPIO de toda acción que recibe un POST.
     *  Compara el token del formulario con el que guardé en la sesión
     *  (se crea en public/index.php). Si no coinciden, el formulario no
     *  viene de mi web -> paro aquí con un error 403 (Prohibido).
     *  hash_equals() compara en tiempo constante (evita "timing attacks").
     * ================================================================= */
    protected function checkCsrf(): void
    {
        // El token que llega oculto en el formulario (o '' si no viene)
        $token = $_POST['csrf_token'] ?? '';

        /* is_string: si alguien manda csrf_token[]=x llega un array, y
         * hash_equals() daría un error 500. Así se rechaza con un 403 normal */
        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $this->error(403, 'Token CSRF inválido. Recarga la página e inténtalo de nuevo.');
        }
    }

    /* =================================================================
     *  redirect(): manda al navegador a otra URL, opcionalmente con un
     *  mensaje que se guarda en la sesión y que mostrará la siguiente
     *  página (src/Views/partials/header.php). $type: 'success' o 'error'
     *  exit corta la ejecución: después de redirigir no se hace nada más.
     * ================================================================= */
    protected function redirect(string $url, string $message = '', string $type = 'success'): void
    {
        if ($message !== '') {
            $_SESSION[$type] = $message;
        }

        header("Location: $url");
        exit;
    }

    /* =================================================================
     *  requireLogin(): AUTENTICACIÓN (¿sé quién es?)
     *  Si no hay usuario en la sesión, lo mando al login con un aviso.
     * ================================================================= */
    protected function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login', 'Tienes que iniciar sesión para hacer eso.', 'error');
        }
    }

    /* =================================================================
     *  error(): página de error con su código HTTP y paro.
     *  404 = no existe    403 = existe, pero no tienes permiso
     *  exit corta la ejecución: lo que haya después ya no se ejecuta.
     * ================================================================= */
    protected function error(int $code, string $message): void
    {
        http_response_code($code);
        require __DIR__ . '/../Views/error.php';
        exit;
    }
}
