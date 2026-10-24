<?php
/* #####################################################################
 *  CABECERA COMÚN: la incluyen todas las vistas al principio.
 *  - Menú: si hay sesión, muestra el email y "Cerrar sesión";
 *    si no, "Iniciar sesión" y "Registrarse".
 *  - Mensajes de éxito/error que dejó redirect() en la sesión.
 *    Se borran con unset() para que salgan solo una vez.
 *  Cada vista pone $pageTitle antes de incluir este archivo.
 *
 *  Sintaxis de PHP en las vistas:
 *  - <?= algo ?>  es lo mismo que <?php echo algo; ?>
 *  - if (...): ... else: ... endif;  es un if normal, pero escrito así
 *    se lee mejor cuando hay HTML en medio (igual con foreach/endforeach)
 * ##################################################################### */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Tablón') ?></title>
    <!-- Los estilos de toda la web (public/css/style.css) -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- Menú: cambia según haya o no un usuario en la sesión -->
    <nav>
        <a href="/posts" class="marca">📋 Tablón de anuncios</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/posts/create">➕ Nuevo anuncio</a>
            <span class="usuario">👤 <?= htmlspecialchars($_SESSION['email']) ?></span>
            <a href="/logout">Cerrar sesión</a>
        <?php else: ?>
            <a href="/login">Iniciar sesión</a>
            <a href="/register">Registrarse</a>
        <?php endif; ?>
    </nav>

    <!-- Todo el contenido de la página va dentro de <main> (lo cierra footer.php) -->
    <main>

    <?php if (!empty($_SESSION['success'])): ?>
        <p class="aviso aviso-exito"><?= htmlspecialchars($_SESSION['success']) ?></p>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <p class="aviso aviso-error"><?= htmlspecialchars($_SESSION['error']) ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
