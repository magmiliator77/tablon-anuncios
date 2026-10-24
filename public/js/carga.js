/* #####################################################################
 *  PANTALLA DE CARGA (la incluye footer.php en todas las páginas)
 *  Al pulsar un enlace o enviar un formulario, si la página siguiente
 *  tarda más de 400 ms en llegar, sale el elefante de PHP corriendo.
 *  Si carga rápido, no da tiempo a verla (no molesta).
 *  Cuando llega la página nueva, esta desaparece sola con todo lo demás.
 *
 *  Caso especial: al PUBLICAR un anuncio nuevo la pantalla sale siempre,
 *  durante 2 segundos, y luego se envía el formulario.
 * ##################################################################### */

var pantalla = document.getElementById('pantalla-carga');
var temporizador = null;

// Enseña la pantalla de carga (le pone la clase "visible" del CSS)
function mostrarPantalla() {
    pantalla.classList.add('visible');
}

// Espera 400 ms y, si la página sigue sin cambiar, enseña la pantalla
function empezarCarga() {
    temporizador = setTimeout(mostrarPantalla, 400);
}

// Al pulsar un enlace de la propia web (los que empiezan por "/")
function alPulsar(evento) {
    var enlace = evento.target.closest('a');   // El enlace pulsado (o null)

    if (enlace !== null && enlace.getAttribute('href').startsWith('/')) {
        empezarCarga();
    }
}

// Al enviar un formulario
function alEnviar(evento) {
    // Si se ha cancelado (por ejemplo, el confirm() de borrar), no hago nada
    if (evento.defaultPrevented) {
        return;
    }

    var formulario = evento.target;

    /* Publicar anuncio nuevo (el formulario de create.php): paro el envío,
     * enseño ya la pantalla y envío el formulario 2 segundos después.
     * formulario.submit() NO vuelve a lanzar este evento, así que no se repite. */
    if (formulario.getAttribute('action') === '/posts' && formulario.getAttribute('method') === 'post') {
        evento.preventDefault();
        mostrarPantalla();
        setTimeout(function () {
            formulario.submit();
        }, 2000);
        return;
    }

    empezarCarga();
}

/* Al volver con el botón "Atrás", el navegador puede enseñar la página
 * guardada en memoria: me aseguro de que la pantalla no siga puesta */
function alVolver() {
    clearTimeout(temporizador);
    pantalla.classList.remove('visible');
}

document.addEventListener('click', alPulsar);
document.addEventListener('submit', alEnviar);
window.addEventListener('pageshow', alVolver);
