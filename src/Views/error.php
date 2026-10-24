<?php
/* Página de error. La usa Controller::error() con $code (404, 403...) y $message */

// Solo para el editor: $code y $message son los parámetros de error()
/** @var int $code */
/** @var string $message */

$pageTitle = "Error $code";
require __DIR__ . '/partials/header.php';
?>

<div class="centrado">
<div class="icono-titulo icono-error">⚠️</div>
<h1>Error <?= htmlspecialchars((string) $code) ?></h1>
<p><?= htmlspecialchars($message) ?></p>
<p><a href="/posts" class="boton boton-secundario">← Volver al tablón</a></p>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
