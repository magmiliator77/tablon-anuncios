<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Comment;
use App\Models\Post;

/* #####################################################################
 *  ANUNCIOS: index, show, create, store, edit, update, destroy
 *  - Ver y buscar: cualquiera.
 *  - Crear: hay que estar logueado (AUTENTICACIÓN).
 *  - Editar y borrar: solo el autor del anuncio (AUTORIZACIÓN).
 * ##################################################################### */

class PostController extends Controller
{
    /* =================================================================
     *  GET /posts  o  /posts?q=texto -> listado (filtrado si se busca algo)
     * ================================================================= */
    public function index(): void
    {
        /* $_GET['q'] es lo que se escribió en el buscador (?q=...).
         * ?? '' = si no viene nada (listado normal), uso un texto vacío */
        $search = trim($_GET['q'] ?? '');

        // Creo el modelo pasándole la conexión y le pido los anuncios
        $posts = (new Post($this->db))->search($search);

        /* La vista se "pega" aquí con require, así que puede usar
         * directamente las variables $posts y $search */
        require __DIR__ . '/../Views/posts/index.php';
    }

    /* =================================================================
     *  GET /posts/show?id=3 -> un anuncio con sus comentarios
     * ================================================================= */
    public function show(): void
    {
        /* El id llega como texto en la URL (?id=3). (int) lo convierte en número;
         * si no viene o es basura ("abc"), queda 0 y no existirá ningún anuncio */
        $id = (int) ($_GET['id'] ?? 0);
        $post = (new Post($this->db))->getByIdWithAuthor($id);

        if ($post === null) {
            $this->error(404, 'Ese anuncio no existe.');
        }

        // Relación 1:N -> los comentarios de ESTE anuncio
        $comments = (new Comment($this->db))->getByPost($id);

        require __DIR__ . '/../Views/posts/show.php';
    }

    // GET /posts/create -> formulario de anuncio nuevo (solo logueados)
    public function create(): void
    {
        $this->requireLogin();

        require __DIR__ . '/../Views/posts/create.php';
    }

    /* =================================================================
     *  POST /posts -> guarda el anuncio nuevo
     * ================================================================= */
    public function store(): void
    {
        $this->checkCsrf();
        $this->requireLogin();

        // Si falta algún campo, readForm() me devuelve al formulario
        $data = $this->readForm('/posts/create');

        /* El autor se saca de la SESIÓN, nunca del formulario
         * (un campo del formulario lo puede cambiar cualquiera) */
        $data['user_id'] = $_SESSION['user_id'];

        (new Post($this->db))->insert($data);

        $this->redirect('/posts', 'Anuncio publicado.');
    }

    /* =================================================================
     *  GET /posts/edit?id=3 -> formulario de edición (solo el autor)
     * ================================================================= */
    public function edit(): void
    {
        $this->requireLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $post = $this->findOwnPost($id);   // Si no es suyo, se para aquí con un 403

        require __DIR__ . '/../Views/posts/edit.php';
    }

    /* =================================================================
     *  POST /posts/update -> guarda los cambios (solo el autor)
     *  Vuelvo a comprobar que es el autor AQUÍ TAMBIÉN: aunque el botón
     *  de editar no se vea, alguien podría mandar el POST a mano.
     * ================================================================= */
    public function update(): void
    {
        $this->checkCsrf();
        $this->requireLogin();

        /* Aquí el id viene del formulario ($_POST), no de la URL ($_GET).
         * No me fío de él: findOwnPost() comprueba que el anuncio sea mío */
        $id = (int) ($_POST['id'] ?? 0);
        $this->findOwnPost($id);

        $data = $this->readForm("/posts/edit?id=$id");

        (new Post($this->db))->update($id, $data);

        $this->redirect("/posts/show?id=$id", 'Anuncio actualizado.');
    }

    /* =================================================================
     *  POST /posts/delete -> borra el anuncio (solo el autor)
     * ================================================================= */
    public function destroy(): void
    {
        $this->checkCsrf();
        $this->requireLogin();

        $id = (int) ($_POST['id'] ?? 0);
        $this->findOwnPost($id);

        (new Post($this->db))->delete($id);

        $this->redirect('/posts', 'Anuncio eliminado.');
    }

    /* =================================================================
     *  findOwnPost(): AUTORIZACIÓN (¿le dejo hacer esto?)
     *  Busca el anuncio y comprueba que su user_id es el del usuario
     *  de la sesión. Si no existe -> 404. Si no es suyo -> 403 Prohibido.
     *  Uso (int) en los dos lados para comparar número con número.
     * ================================================================= */
    private function findOwnPost(int $id): array
    {
        $post = (new Post($this->db))->getById($id);

        if ($post === null) {
            $this->error(404, 'Ese anuncio no existe.');
        }

        if ((int) $post['user_id'] !== (int) $_SESSION['user_id']) {
            $this->error(403, 'No tienes permiso para hacer esto: el anuncio no es tuyo.');
        }

        return $post;
    }

    /* =================================================================
     *  readForm(): lee y valida los campos del formulario de anuncio.
     *  Lo usan store() y update(), así no lo escribo dos veces.
     *  Si falta algo, vuelve a $backUrl con un mensaje de error.
     * ================================================================= */
    private function readForm(string $backUrl): array
    {
        $data = [
            'title'    => trim($_POST['title'] ?? ''),
            'body'     => trim($_POST['body'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
        ];

        if ($data['title'] === '' || $data['body'] === '' || $data['category'] === '') {
            $this->redirect($backUrl, 'Título, texto y categoría son obligatorios.', 'error');
        }

        return $data;
    }
}
