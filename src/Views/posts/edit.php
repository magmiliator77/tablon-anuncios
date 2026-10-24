<?php
/* Formulario de edición. Lo muestra PostController::edit() con $post
 * y lo recibe PostController::update().
 * Los campos llevan value="..." para que salgan rellenos con lo que había. */

/** @var array $post */  // Solo para el editor: $post lo crea el controlador

$pageTitle = 'Editar anuncio';
require __DIR__ . '/../partials/header.php';
?>

<h1>Editar anuncio</h1>

<!-- El id va en la URL del formulario: /posts/update/3 -->
<form action="/posts/update/<?= htmlspecialchars((string) $post['id']) ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <label>Título: <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required></label><br>

    <label>Categoría: <input type="text" name="category" value="<?= htmlspecialchars($post['category']) ?>" required></label><br>

    <label>Texto:<br><textarea name="body" rows="5" cols="50" required><?= htmlspecialchars($post['body']) ?></textarea></label><br>

    <button type="submit">Guardar cambios</button>
</form>

<p><a href="/posts/<?= htmlspecialchars((string) $post['id']) ?>" class="boton boton-secundario">← Cancelar</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
