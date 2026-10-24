/* #####################################################################
 *  PANTALLA DE CARGA (la incluye footer.php en todas las páginas)
 *  Al pulsar un enlace o enviar un formulario, si la página siguiente
 *  tarda más de 400 ms en llegar, sale el elefante de PHP corriendo.
 *  Si carga rápido, no da tiempo a verla (no molesta).
 *  Cuando llega la página nueva, esta desaparece sola con todo lo demás.
 * ##################################################################### */

const pantalla = document.getElementById('pantalla-carga');
let temporizador = null;

function empezarCarga() {
    clearTimeout(temporizador);
    temporizador = setTimeout(() => pantalla.classList.add('visible'), 400);
}

if (pantalla) {
    // Enlaces de la propia web (no los que abren otra pestaña o van a #)
    document.addEventListener('click', (evento) => {
        const enlace = evento.target.closest('a');
        if (enlace && enlace.origin === location.origin && !enlace.hash && enlace.target !== '_blank') {
            empezarCarga();
        }
    });

    // Formularios: solo si de verdad se envían (el confirm() de borrar puede cancelarlo)
    document.addEventListener('submit', (evento) => {
        if (!evento.defaultPrevented) {
            empezarCarga();
        }
    });

    // Al volver con el botón "Atrás" el navegador puede mostrar la página
    // guardada en memoria: me aseguro de que la pantalla no siga puesta
    window.addEventListener('pageshow', () => {
        clearTimeout(temporizador);
        pantalla.classList.remove('visible');
    });
}
