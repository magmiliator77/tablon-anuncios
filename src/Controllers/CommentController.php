<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Comment;
use App\Models\Post;

/* #####################################################################
 *  COMENTARIOS
 *  Solo hace falta una acción: store() (guardar un comentario).
 *  Mostrarlos ya lo hace PostController::show(), y en esta práctica
 *  no se pide editarlos ni borrarlos.
 * ##################################################################### */

class CommentController extends Controller
{
    /* =================================================================
     *  POST /comments -> guarda un comentario en un anuncio
     * ================================================================= */
    public function store(): void
    {
        // 1 · Token CSRF y estar logueado
        $this->checkCsrf();
        $this->requireLogin();

        /* 2 · ¿En qué anuncio? Ese dato sí viene del formulario (campo oculto)
         *     y no pasa nada: cambiarlo solo haría comentar en otro anuncio.
         *     Compruebo que el anuncio existe. */
        $postId = (int) ($_POST['post_id'] ?? 0);
        $body = trim($_POST['body'] ?? '');

        if ((new Post($this->db))->getById($postId) === null) {
            $this->error(404, 'Ese anuncio no existe.');
        }

        if ($body === '') {
            $this->redirect("/posts/show?id=$postId", 'El comentario no puede estar vacío.', 'error');
        }

        /* 3 · Guardo. El autor (user_id) sale de la SESIÓN, nunca del
         *     formulario: si viniera de un campo oculto, cualquiera podría
         *     cambiarlo y comentar haciéndose pasar por otro usuario. */
        (new Comment($this->db))->insert([
            'post_id' => $postId,
            'user_id' => $_SESSION['user_id'],
            'body'    => $body,
        ]);

        $this->redirect("/posts/show?id=$postId", 'Comentario publicado.');
    }
}
