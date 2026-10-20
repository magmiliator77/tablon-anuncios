<?php
/* Página de error. La usa Controller::error() con $code (404, 403...) y $message */
$pageTitle = "Error $code";
require __DIR__ . '/partials/header.php';
?>

<h1>Error <?= $code ?></h1>
<p><?= htmlspecialchars($message) ?></p>
<p><a href="/posts">← Volver al tablón</a></p>

<?php require __DIR__ . '/partials/footer.php'; ?>
