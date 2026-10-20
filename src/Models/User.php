<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/* #####################################################################
 *  MODELO User (tabla users)
 *  Hereda getAll, getById, insert, update y delete de BaseModel.
 *  Solo añade lo que necesita el login y el registro.
 * ##################################################################### */

class User extends BaseModel
{
    public function __construct(Database $db)
    {
        // Llamo al constructor del padre diciéndole qué tabla uso
        parent::__construct($db, 'users');
    }

    /* =================================================================
     *  findByEmail(): para el LOGIN
     *  Devuelve el id del usuario si email y contraseña son correctos,
     *  o null si no.
     *  1 · Busco al usuario SOLO por email.
     *  2 · password_verify() comprueba si la contraseña escrita ('1234')
     *      corresponde con el hash guardado en la BD ('$2y$10$...').
     *      No se puede hacer con un WHERE password = ..., porque
     *      password_hash() da un resultado distinto cada vez (lleva una
     *      "sal" aleatoria dentro).
     * ================================================================= */
    public function findByEmail(string $email, string $password): ?int
    {
        $user = $this->db->fetchOne('SELECT * FROM users WHERE email = :email', ['email' => $email]);

        if ($user === null || !password_verify($password, $user['password'])) {
            return null;
        }

        return (int) $user['id'];
    }

    /* =================================================================
     *  emailExists(): para el REGISTRO, saber si el email ya está cogido
     * ================================================================= */
    public function emailExists(string $email): bool
    {
        // fetchOne() devuelve la fila o null: si NO es null, el email ya está cogido
        return $this->db->fetchOne('SELECT id FROM users WHERE email = :email', ['email' => $email]) !== null;
    }
}
