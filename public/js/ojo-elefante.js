/* #####################################################################
 *  OJO DEL ELEFANTE QUE SIGUE AL RATÓN (solo en la portada, posts/index.php)
 *  Mientras el ratón esté dentro de la cabecera "Tablón de anuncios",
 *  la pupila se desplaza hacia donde está el puntero.
 *  Al salir, vuelve a su sitio.
 * ##################################################################### */

const portada = document.querySelector('.portada');
const ojo = document.querySelector('.ojo');
const pupila = document.querySelector('.pupila');

// Mueve la pupila desde el centro del ojo (x, y en píxeles)
function moverPupila(x, y) {
    pupila.style.transform = `translate(calc(-50% + ${x}px), calc(-50% + ${y}px))`;
}

// Posición de reposo: un poco a la derecha, como en el dibujo original
function reposo() {
    moverPupila(ojo.offsetWidth * 0.2, 0);
}

if (portada && ojo && pupila) {
    portada.addEventListener('mousemove', (evento) => {
        // Centro del ojo en la pantalla
        const caja = ojo.getBoundingClientRect();
        const centroX = caja.left + caja.width / 2;
        const centroY = caja.top + caja.height / 2;

        // Ángulo hacia el ratón (como Math.atan2 en Java)
        const angulo = Math.atan2(evento.clientY - centroY, evento.clientX - centroX);

        // Lo máximo que puede moverse sin salirse de lo blanco
        // (el ojo recorta lo que sobresale, así que puede llegar al borde)
        const maxX = caja.width * 0.22;
        const maxY = caja.height * 0.2;

        moverPupila(Math.cos(angulo) * maxX, Math.sin(angulo) * maxY);
    });

    portada.addEventListener('mouseleave', reposo);
    reposo();
}
