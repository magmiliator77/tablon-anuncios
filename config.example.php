<?php

/* #####################################################################
 *  CONFIGURACIÓN (plantilla)
 *  Lo copio como config.php y pongo ahí mis datos.
 *  Con Docker no hace falta: el contenedor web lo copia solo al arrancar.
 *
 *  OJO: host es "mysql" y no "localhost" porque PHP corre dentro del
 *  contenedor "web", y para él la base de datos está en otro contenedor
 *  que se llama "mysql" (el nombre del servicio en docker-compose.yml).
 * ##################################################################### */

return [
    'db' => [
        'host'    => 'mysql',
        'dbname'  => 'tablon_anuncios',
        'user'    => 'root',
        'pass'    => 'Tablon_2026',
        'charset' => 'utf8mb4',
    ],
];
