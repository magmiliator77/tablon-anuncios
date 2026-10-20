<?php
/* Formulario de registro. Lo muestra AuthController::showRegisterForm()
 * y lo recibe AuthController::register() */
$pageTitle = 'Registrarse';
require __DIR__ . '/../partials/header.php';
?>

<h1>Crear cuenta</h1>

<form action="/register" method="post">
    <!-- Token CSRF oculto: lo comprueba checkCsrf() al recibir el formulario -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <label>Nombre de usuario: <input type="text" name="username" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Contraseña: <input type="password" name="password" required></label><br>

    <button type="submit">Registrarme</button>
</form>

<p>¿Ya tienes cuenta? <a href="/login">Inicia sesión</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
