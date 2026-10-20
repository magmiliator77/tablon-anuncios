<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

// #####################################################################
//  MODELO BASE (clase abstracta, como en Java)
//  Tiene el código que es IGUAL para todas las tablas: obtener todos,
//  obtener por id, insertar, modificar y borrar.
//  User, Post y Comment heredan de aquí y solo añaden lo suyo.
//  "abstract" = no se puede hacer new BaseModel(), solo new Post(), etc.
// #####################################################################

abstract class BaseModel
{
    // Constructor con promoción de propiedades: crea $this->db y $this->table
    // "protected" = las clases hijas (Post, User...) también pueden usarlas
    public function __construct(
        protected Database $db,
        protected string $table,
    ) {
    }

    // Todos los registros de la tabla (o null si está vacía)
    public function getAll(): ?array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table}");
    }

    // Un registro por su id (o null si no existe)
    public function getById(int $id): ?array
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    // Borrar por id.
    // Pregunta del enunciado: ¿lo meto en Database o aquí?
    // Lo dejo AQUÍ: Database no sabe nada de tablas ni de ids, solo
    // ejecuta SQL. "Borrar un registro por id" es algo de los modelos.
    // Para lanzar el DELETE me basta con el query() genérico de Database.
    public function delete(int $id): void
    {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    // =================================================================
    //  insert(['title' => 'Hola', 'body' => '...'])
    //  Construye:  INSERT INTO posts (title, body) VALUES (:title, :body)
    //  Los NOMBRES de columna salen de las claves del array, que las
    //  escribo yo en el controlador (nunca vienen del usuario).
    //  Los VALORES van como parámetros de la sentencia preparada.
    // =================================================================
    public function insert(array $data): void
    {
        $columns = array_keys($data);                  // ['title', 'body']
        $columnList = implode(', ', $columns);         // "title, body"
        $placeholders = ':' . implode(', :', $columns); // ":title, :body"

        $sql = "INSERT INTO {$this->table} ($columnList) VALUES ($placeholders)";
        $this->db->query($sql, $data);
    }

    // =================================================================
    //  update(3, ['title' => 'Nuevo título'])
    //  Construye:  UPDATE posts SET title = :title WHERE id = :id
    // =================================================================
    public function update(int $id, array $data): void
    {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "$column = :$column";            // "title = :title"
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = :id";

        $data['id'] = $id;   // Añado el id para rellenar el hueco :id
        $this->db->query($sql, $data);
    }
}
