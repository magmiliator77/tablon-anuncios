<?php
/* #####################################################################
 *  DETALLE DE UN ANUNCIO + SUS COMENTARIOS
 *  Variables que le pasa PostController::show():
 *   $post     -> el anuncio (con 'username' del autor)
 *   $comments -> sus comentarios (o null si no tiene)
 * ##################################################################### */

/* Las @var solo son para el editor (VS Code): le dicen que estas variables existen porque las crea el controlador antes del require */

/** @var array $post */
/** @var array|null $comments */


$pageTitle = $post['title'];
require __DIR__ . '/../partials/header.php';

/* ¿El usuario logueado es el autor? Solo entonces enseño Editar/Borrar.
 * OJO: esto solo ESCONDE los botones. La protección de verdad está en
 * el controlador (findOwnPost), por si alguien manda la petición a mano. */
$isOwner = isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $post['user_id'];
?>

<h1><?= htmlspecialchars($post['title']) ?></h1>
<p class="datos">
    <span class="etiqueta"><?= htmlspecialchars($post['category']) ?></span> ·
    <strong>Autor:</strong> <?= htmlspecialchars($post['username']) ?> ·
    <?= htmlspecialchars($post['created_at']) ?>
</p>

<!-- nl2br() convierte los saltos de línea en <br>. Va DESPUÉS de htmlspecialchars -->
<p class="cuerpo"><?= nl2br(htmlspecialchars($post['body'])) ?></p>

<?php if ($isOwner): ?>
<div class="acciones">
    <!-- Es un enlace (lleva a otra página), pero con aspecto de botón (clase .boton del CSS) -->
    <a href="/posts/edit?id=<?= htmlspecialchars((string) $post['id']) ?>" class="boton boton-editar">✏️ Editar</a>

    <!-- Borrar va por POST (nunca con un enlace GET) y con token CSRF -->
    <form action="/posts/delete" method="post" style="display:inline"
          onsubmit="return confirm('¿Seguro que quieres borrar este anuncio?');">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) $post['id']) ?>">

        <button type="submit">🗑️ Borrar</button>
    </form>
</div>
<?php endif; ?>

<hr>

<h2>Comentarios</h2>

<?php if ($comments === null): ?>
    <p>Todavía no hay comentarios.</p>
<?php else: ?>
    <!-- Vienen ya ordenados del más antiguo al más nuevo (ORDER BY en getByPost) -->
    <?php foreach ($comments as $comment): ?>
        <p class="comentario">
            <strong><?= htmlspecialchars($comment['username']) ?></strong>
            <span class="fecha"><?= htmlspecialchars($comment['created_at']) ?></span><br>
            <?= nl2br(htmlspecialchars($comment['body'])) ?>
        </p>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Formulario de comentario: solo si estás logueado.
         post_id va oculto (en qué anuncio comentas). El user_id NO va aquí:
         el controlador lo saca de la sesión. -->
    <form action="/comments" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="post_id" value="<?= htmlspecialchars((string) $post['id']) ?>">

        <textarea name="body" rows="3" cols="50" required></textarea><br>

        <button type="submit">Comentar</button>
    </form>
<?php else: ?>
    <p><a href="/login">Inicia sesión</a> para comentar.</p>
<?php endif; ?>

<p><a href="/posts">← Volver al tablón</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
