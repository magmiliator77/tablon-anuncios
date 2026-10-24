<?php /* PIE COMÚN: cierra lo que abrió header.php */ ?>
    </main>

    <footer>
        Hecho con <span class="etiqueta">PHP 8.3</span> · Práctica final 2.8
    </footer>

    <!-- Pantalla de carga: el elefante de PHP corriendo por el suelo.
         Está escondida; la enseña public/js/carga.js si la página tarda -->
    <div id="pantalla-carga" aria-hidden="true">
        <div class="pista">
            <img src="/img/elefante-corriendo.webp" alt="" class="corredor">
        </div>
        <p>Cargando…</p>
    </div>
    <script src="/js/carga.js?v=3"></script>
</body>
</html>
