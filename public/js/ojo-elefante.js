/* #####################################################################
 *  OJO DEL ELEFANTE QUE SIGUE AL RATÓN (solo en la portada, posts/index.php)
 *  Mientras el ratón esté dentro de la cabecera "Tablón de anuncios",
 *  la pupila se desplaza hacia donde está el puntero.
 *  Al salir, vuelve a su sitio.
 *
 *  Para que vaya fluido:
 *  - El ratón solo apunta el DESTINO de la pupila (destinoX, destinoY).
 *  - Un bucle con requestAnimationFrame (una vez por fotograma, ~60 por
 *    segundo) acerca la pupila un poco a ese destino en cada vuelta.
 *    Así el movimiento es suave y no va a saltos.
 * ##################################################################### */

const portada = document.querySelector('.portada');
const ojo = document.querySelector('.ojo');
const pupila = document.querySelector('.pupila');

// Dónde está ahora la pupila y adónde quiere ir (píxeles desde el centro del ojo)
let actualX = 0, actualY = 0;
let destinoX = 0, destinoY = 0;

// Posición de reposo: un poco a la derecha, como en el dibujo original
function reposo() {
    destinoX = ojo.offsetWidth * 0.2;
    destinoY = 0;
}

// Se ejecuta en cada fotograma: recorre un 25 % de lo que falta hasta el destino
function animar() {
    actualX += (destinoX - actualX) * 0.25;
    actualY += (destinoY - actualY) * 0.25;
    pupila.style.transform = `translate(calc(-50% + ${actualX}px), calc(-50% + ${actualY}px))`;
    requestAnimationFrame(animar);
}

if (portada && ojo && pupila) {
    portada.addEventListener('mousemove', (evento) => {
        // Centro del ojo en la pantalla
        const caja = ojo.getBoundingClientRect();
        const centroX = caja.left + caja.width / 2;
        const centroY = caja.top + caja.height / 2;

        // Ángulo hacia el ratón (como Math.atan2 en Java)
        const angulo = Math.atan2(evento.clientY - centroY, evento.clientX - centroX);

        // Lo máximo que puede moverse la pupila
        // (el ojo recorta lo que sobresale, así que puede llegar al borde)
        destinoX = Math.cos(angulo) * caja.width * 0.22;
        destinoY = Math.sin(angulo) * caja.height * 0.2;
    });

    portada.addEventListener('mouseleave', reposo);

    reposo();
    actualX = destinoX;   // Empieza ya en su sitio, sin moverse al cargar
    animar();
}
