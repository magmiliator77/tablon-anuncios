<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

/* #####################################################################
 *  AUTENTICACIÓN: registro, login y logout
 * ##################################################################### */

class AuthController extends Controller
{
    // GET /register -> solo muestra el formulario
    public function showRegisterForm(): void
    {
        require __DIR__ . '/../Views/auth/register.php';
    }



    // Aqui tenia una cosa de php demasiado avanzada asi que la he quitado porque no es buena idea.



    /* =================================================================
     *  POST /register -> crea el usuario
     * ================================================================= */
    public function register(): void
    {
        // 1 · Lo primero de todo: comprobar el token CSRF
        $this->checkCsrf();

        // 2 · Leer el formulario. trim() quita espacios al principio y al final
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';   // La contraseña no se recorta

        // 3 · Validar. Si algo falla, vuelvo al formulario con el error
        $userModel = new User($this->db);

        if ($username === '' || $email === '' || $password === '') {
            $this->redirect('/register', 'Rellena todos los campos.', 'error');
        }

        // filter_var() con FILTER_VALIDATE_EMAIL devuelve false si no tiene forma de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/register', 'El email no es válido.', 'error');
        }

        if ($userModel->emailExists($email)) {
            $this->redirect('/register', 'Ya existe un usuario con ese email.', 'error');
        }

        /* 4 · Guardar. La contraseña se guarda como HASH con password_hash(),
         *     nunca en texto plano (ni con md5, que está obsoleto e inseguro) */
        $userModel->insert([
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $this->redirect('/login', 'Cuenta creada. Ya puedes iniciar sesión.');
    }

    // GET /login -> solo muestra el formulario
    public function showLoginForm(): void
    {
        require __DIR__ . '/../Views/auth/login.php';
    }

    /* =================================================================
     *  POST /login -> comprueba email y contraseña
     * ================================================================= */
    public function login(): void
    {
        $this->checkCsrf();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // 1 · findByEmail devuelve el id si los datos son correctos, o null
        $userModel = new User($this->db);
        $userId = $userModel->findByEmail($email, $password);

        if ($userId === null) {
            // No digo si falla el email o la contraseña: así no doy pistas
            $this->redirect('/login', 'Email o contraseña incorrectos.', 'error');
        }

        /* 2 · La sesión ya está arrancada (session_start() en public/index.php).
         *     session_regenerate_id(true) le da al usuario un id de sesión nuevo
         *     al loguearse (protección contra el robo de sesión). */
        session_regenerate_id(true);

        /* 3 · Guardo quién es. Desde ahora, en cualquier página,
         *     $_SESSION['user_id'] me dice qué usuario está logueado. */
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $email;

        $this->redirect('/posts', 'Has iniciado sesión.');
    }

    /* =================================================================
     *  GET /logout -> borra la sesión entera (usuario, token, mensajes...)
     * ================================================================= */
    public function logout(): void
    {
        /* Borra todo lo guardado en la sesión: a partir de aquí ya no hay
         * $_SESSION['user_id'], así que la web me trata como visitante */
        session_destroy();

        $this->redirect('/posts');
    }
}
