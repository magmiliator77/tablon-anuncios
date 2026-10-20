<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/* #####################################################################
 *  MODELO Post (tabla posts = anuncios)
 *  Hereda todo el CRUD de BaseModel y añade el buscador.
 * ##################################################################### */

class Post extends BaseModel
{
    public function __construct(Database $db)
    {
        parent::__construct($db, 'posts');
    }

    /* =================================================================
     *  search(): listado de anuncios filtrado por título o categoría
     *  - JOIN con users para sacar también el nombre del autor.
     *  - LIKE '%texto%' = "que contenga texto" en cualquier posición.
     *  - Si $text está vacío queda LIKE '%%', que coincide con todo:
     *    por eso me sirve también para el listado normal sin buscar.
     *  - El texto del usuario va como parámetro (:title, :category),
     *    nunca pegado al SQL -> no hay inyección SQL.
     * ================================================================= */
    public function search(string $text): ?array
    {
        $sql = 'SELECT posts.*, users.username
                FROM posts
                JOIN users ON users.id = posts.user_id
                WHERE posts.title LIKE :title OR posts.category LIKE :category
                ORDER BY posts.created_at DESC, posts.id DESC';

        return $this->db->fetchAll($sql, [
            'title'    => "%$text%",
            'category' => "%$text%",
        ]);
    }

    // Un anuncio con el nombre de su autor (para la vista de detalle)
    public function getByIdWithAuthor(int $id): ?array
    {
        $sql = 'SELECT posts.*, users.username
                FROM posts
                JOIN users ON users.id = posts.user_id
                WHERE posts.id = :id';

        return $this->db->fetchOne($sql, ['id' => $id]);
    }
}
