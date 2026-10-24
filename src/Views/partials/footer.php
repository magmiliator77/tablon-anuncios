<?php /* PIE COMÚN: cierra lo que abrió header.php */ ?>
    </main>

    <footer>
        <!-- "PHP 8.3" es un enlace a la página oficial de esa versión.
             Parece una etiqueta, pero al pasar el ratón se hunde un poco -->
        <p>
            Hecho con
            <a href="https://www.php.net/releases/8.3/es.php" target="_blank" rel="noopener"
               class="etiqueta etiqueta-enlace" title="Documentación oficial de PHP 8.3">PHP 8.3</a>
            · Práctica final 2.8
        </p>

        <!-- Autor, con el logo de GitHub (dibujado en SVG) que lleva a mi perfil.
             target="_blank" lo abre en otra pestaña -->
        <p class="autor">
            Creado por Miguel Torres Martínez
            <a href="https://github.com/magmiliator77" target="_blank" rel="noopener" title="Mi perfil de GitHub">
                <svg viewBox="0 0 16 16" width="18" height="18" aria-label="GitHub">
                    <path fill="currentColor" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
                </svg>
            </a>
        </p>
    </footer>

    <!-- Pantalla de carga: el elefante de PHP corriendo por el suelo.
         Está escondida; la enseña public/js/carga.js si la página tarda -->
    <div id="pantalla-carga" aria-hidden="true">
        <div class="pista">
            <img src="/img/elefante-corriendo.webp" alt="" class="corredor">
        </div>
        <p>Cargando…</p>
    </div>
    <script src="/js/carga.js?v=4"></script>
</body>
</html>
