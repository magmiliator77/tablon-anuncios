<?php
/* #####################################################################
 *  LISTADO DE ANUNCIOS + BUSCADOR
 *  Variables que le pasa PostController::index():
 *   $posts  -> array de anuncios (o null si no hay ninguno)
 *   $search -> el texto buscado (para dejarlo escrito en el buscador)
 * ##################################################################### */

// Las @var solo son para el editor: estas variables las crea el controlador
/** @var array|null $posts */
/** @var string $search */

$pageTitle = 'Tablón de anuncios';
require __DIR__ . '/../partials/header.php';
?>

<!-- Cabecera grande con el elefante; el buscador va dentro -->
<section class="portada">
<div class="texto">
<h1>Tablón de anuncios</h1>
<p>Compra, vende, busca grupo o pregunta lo que necesites.</p>

<!-- Buscador: method="get" porque buscar no cambia nada en el servidor.
     Manda a /posts?q=lo-que-escribas -->
<form action="/posts" method="get">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por título o categoría">
    <button type="submit">Buscar</button>

    <?php if ($search !== ''): ?>
        <a href="/posts" class="boton boton-secundario">Quitar filtro</a>
    <?php endif; ?>
</form>
</div>

<!-- El elefante de PHP (la mascota oficial). Encima de su ojo va otro ojo
     hecho con HTML, cuya pupila mueve public/js/ojo-elefante.js -->
<div class="elefante">
    <img src="/img/elefante-php.webp" alt="Elefante de PHP">
    <div class="ojo">
        <!-- Pupila con la misma forma que la del dibujo: disco oscuro con
             una muesca "<" a la izquierda y un brillo ovalado a la derecha -->
        <svg class="pupila" viewBox="0 0 66 66" aria-hidden="true">
            <circle cx="33" cy="33" r="33" fill="#2e2a4f"/>
            <polygon points="-2,16 17,34 -2,55" fill="#fff"/>
            <ellipse cx="46.5" cy="27.5" rx="7.5" ry="11.5" fill="#fff"/>
        </svg>
    </div>
</div>
</section>

<!-- fetchAll() devuelve null si no hay filas: entonces enseño un aviso -->
<?php if ($posts === null): ?>
    <p>No hay anuncios.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Título</th>
            <th>Categoría</th>
            <th>Autor</th>
            <th>Fecha</th>
        </tr>

        <!-- Una fila de la tabla por cada anuncio. El título enlaza a su detalle -->
        <?php foreach ($posts as $post): ?>
            <tr>
                <td><a href="/posts/show?id=<?= htmlspecialchars((string) $post['id']) ?>"><?= htmlspecialchars($post['title']) ?></a></td>
                <td><span class="etiqueta"><?= htmlspecialchars($post['category']) ?></span></td>
                <td><?= htmlspecialchars($post['username']) ?></td>
                <td><?= htmlspecialchars($post['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<!-- El JavaScript que hace que el ojo siga al ratón -->
<script src="/js/ojo-elefante.js?v=2"></script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
