<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/* #####################################################################
 *  MODELO Comment (tabla comments)
 *  Relación 1:N -> un anuncio (post) tiene muchos comentarios.
 *  Para guardar uno me vale el insert() heredado de BaseModel.
 *  Solo añado cómo sacar los comentarios de UN anuncio.
 * ##################################################################### */

class Comment extends BaseModel
{
    public function __construct(Database $db)
    {
        parent::__construct($db, 'comments');
    }

    /* Comentarios de un anuncio, del más antiguo al más nuevo,
     * con el nombre de quien lo escribió (JOIN con users) */
    public function getByPost(int $postId): ?array
    {
        $sql = 'SELECT comments.*, users.username
                FROM comments
                JOIN users ON users.id = comments.user_id
                WHERE comments.post_id = :post_id
                ORDER BY comments.created_at ASC, comments.id ASC';

        return $this->db->fetchAll($sql, ['post_id' => $postId]);
    }
}
