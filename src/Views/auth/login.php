<?php
/* Formulario de login. Lo muestra AuthController::showLoginForm()
 * y lo recibe AuthController::login() */
$pageTitle = 'Iniciar sesión';
require __DIR__ . '/../partials/header.php';
?>

<h1>Iniciar sesión</h1>

<form action="/login" method="post">
    <!-- El token CSRF también va en el login: lo pide el enunciado en TODOS los POST -->
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <label>Email: <input type="email" name="email" required></label><br>

    <label>Contraseña: <input type="password" name="password" required></label><br>

    <button type="submit">Entrar</button>
</form>

<p>¿No tienes cuenta? <a href="/register">Regístrate</a></p>

<?php require __DIR__ . '/../partials/footer.php'; ?>
