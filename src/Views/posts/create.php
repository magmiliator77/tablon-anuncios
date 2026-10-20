<?php
/* Formulario de anuncio nuevo. Lo muestra PostController::create()
 * y lo recibe PostController::store() */
$pageTitle = 'Nuevo anuncio';
require __DIR__ . '/../partials/header.php';
?>

<h1>Nuevo anuncio</h1>

<!-- required hace que el navegador no deje enviar campos vacíos, pero NO
     es seguridad (se quita con F12): por eso el controlador vuelve a validar -->
<form action="/posts" method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <label>Título: <input type="text" name="title" required></label><br>

    <label>Categoría: <input type="text" name="category" placeholder="Venta, Clase..." required></label><br>

    <label>Texto:<br><textarea name="body" rows="5" cols="50" required></textarea></label><br>

    <button type="submit">Publicar</button>
</form>

<p><a href="/posts">← Volver al tablón</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
