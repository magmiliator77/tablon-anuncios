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

<h1>Tablón de anuncios</h1>

<!-- Buscador: method="get" porque buscar no cambia nada en el servidor.
     Manda a /posts?q=lo-que-escribas -->
<form action="/posts" method="get">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por título o categoría">
    <button type="submit">Buscar</button>

    <?php if ($search !== ''): ?>
        <a href="/posts">Quitar filtro</a>
    <?php endif; ?>
</form>

<!-- fetchAll() devuelve null si no hay filas: entonces enseño un aviso -->
<?php if ($posts === null): ?>
    <p>No hay anuncios.</p>
<?php else: ?>
    <table border="1" cellpadding="6">
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
                <td><?= htmlspecialchars($post['category']) ?></td>
                <td><?= htmlspecialchars($post['username']) ?></td>
                <td><?= htmlspecialchars($post['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
