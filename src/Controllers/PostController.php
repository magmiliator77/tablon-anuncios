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
        $postModel = new Post($this->db);
        $posts = $postModel->search($search);

        /* La vista se "pega" aquí con require, así que puede usar
         * directamente las variables $posts y $search */
        require __DIR__ . '/../Views/posts/index.php';
    }

    /* =================================================================
     *  GET /posts/3 -> un anuncio con sus comentarios
     *  $id lo saca el Router de la URL (el 3 de /posts/3) y me lo pasa
     * ================================================================= */
    public function show(int $id): void
    {
        $postModel = new Post($this->db);
        $post = $postModel->getByIdWithAuthor($id);

        if ($post === null) {
            $this->error(404, 'Ese anuncio no existe.');
        }

        // Relación 1:N -> los comentarios de ESTE anuncio
        $commentModel = new Comment($this->db);
        $comments = $commentModel->getByPost($id);

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

        $postModel = new Post($this->db);
        $postModel->insert($data);

        $this->redirect('/posts', 'Anuncio publicado.');
    }

    /* =================================================================
     *  GET /posts/edit/3 -> formulario de edición (solo el autor)
     * ================================================================= */
    public function edit(int $id): void
    {
        $this->requireLogin();

        $post = $this->findOwnPost($id);   // Si no es suyo, se para aquí con un 403

        require __DIR__ . '/../Views/posts/edit.php';
    }

    /* =================================================================
     *  POST /posts/update/3 -> guarda los cambios (solo el autor)
     *  Vuelvo a comprobar que es el autor AQUÍ TAMBIÉN: aunque el botón
     *  de editar no se vea, alguien podría mandar el POST a mano.
     * ================================================================= */
    public function update(int $id): void
    {
        $this->checkCsrf();
        $this->requireLogin();

        /* El id viene de la URL, y la URL la puede cambiar cualquiera.
         * No me fío de él: findOwnPost() comprueba que el anuncio sea mío */
        $this->findOwnPost($id);

        $data = $this->readForm("/posts/edit/$id");

        $postModel = new Post($this->db);
        $postModel->update($id, $data);

        $this->redirect("/posts/$id", 'Anuncio actualizado.');
    }

    /* =================================================================
     *  POST /posts/delete/3 -> borra el anuncio (solo el autor)
     * ================================================================= */
    public function destroy(int $id): void
    {
        $this->checkCsrf();
        $this->requireLogin();

        $this->findOwnPost($id);

        $postModel = new Post($this->db);
        $postModel->delete($id);

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
        $postModel = new Post($this->db);
        $post = $postModel->getById($id);

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
