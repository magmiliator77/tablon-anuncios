<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

/* #####################################################################
 *  CAPA DE ACCESO A DATOS
 *  Es la ÚNICA clase que crea la conexión PDO y hace prepare/execute.
 *  Los modelos nunca tocan PDO: le piden las cosas a esta clase.
 *  (Principio "no te repitas": el código de conexión está en un solo sitio)
 * ##################################################################### */

class Database
{
    private PDO $pdo;   // La conexión con MySQL

    /* =================================================================
     *  Constructor: lee config.php y se conecta a MySQL
     * ================================================================= */
    public function __construct()
    {
        // 1 · require de un archivo que hace "return [...]" me devuelve ese array
        $config = require __DIR__ . '/../../config.php';
        $db = $config['db'];

        // 2 · DSN = "dirección" de la base de datos
        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";

        /* 3 · Abro la conexión. Si MySQL no está arrancado o los datos de
         *     config.php están mal, PDO lanza una PDOException: la capturo
         *     para enseñar un mensaje claro en vez de un "Fatal error" (como en el 2.7) */
        try {
            $this->pdo = new PDO($dsn, $db['user'], $db['pass'], [
                // Si una consulta falla, PDO lanza una excepción (en vez de devolver false sin avisar)
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Las filas llegan como arrays asociativos: ['id' => 1, 'title' => '...']
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    /* =================================================================
     *  query(): prepara y ejecuta CUALQUIER consulta y devuelve el PDOStatement
     *  Ejemplo: $db->query('SELECT * FROM users WHERE id = :id', ['id' => 1]);
     *  Es una SENTENCIA PREPARADA: los valores van aparte en $params, nunca
     *  pegados al SQL. Así evito la inyección SQL.
     * ================================================================= */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);  // 1 · MySQL recibe el SQL con los huecos (:id)
        $stmt->execute($params);            // 2 · Relleno los huecos con los valores
        return $stmt;
    }

    /* =================================================================
     *  fetchAll(): igual que query(), pero devuelve TODAS las filas en un array
     *  (o null si no hay ninguna)
     * ================================================================= */
    public function fetchAll(string $sql, array $params = []): ?array
    {
        $rows = $this->query($sql, $params)->fetchAll();
        return $rows === [] ? null : $rows;
    }

    /* =================================================================
     *  fetchOne(): igual, pero devuelve solo la PRIMERA fila (o null)
     *  fetch() devuelve false cuando no hay filas; lo cambio por null.
     * ================================================================= */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }
}
