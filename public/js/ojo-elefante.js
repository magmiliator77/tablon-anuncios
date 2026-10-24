/* #####################################################################
 *  OJO DEL ELEFANTE QUE SIGUE AL RATÓN (solo en la portada, posts/index.php)
 *  Mientras el ratón esté dentro de la cabecera "Tablón de anuncios",
 *  la pupila se desplaza hacia donde está el puntero.
 *  Al salir, vuelve a su sitio.
 *
 *  Para que vaya fluido:
 *  - El ratón solo apunta el DESTINO de la pupila (destinoX, destinoY).
 *  - La función animar() se repite en cada fotograma (unas 60 veces por
 *    segundo) y acerca la pupila un poco a ese destino cada vez.
 * ##################################################################### */

var portada = document.querySelector('.portada');
var ojo = document.querySelector('.ojo');
var pupila = document.querySelector('.pupila');

// Dónde está ahora la pupila y adónde quiere ir (píxeles desde el centro del ojo)
var actualX = 0;
var actualY = 0;
var destinoX = 0;
var destinoY = 0;

// Posición de reposo: un poco a la derecha, como en el dibujo original
function reposo() {
    destinoX = ojo.offsetWidth * 0.2;
    destinoY = 0;
}

// Cada vez que se mueve el ratón dentro de la portada
function seguirRaton(evento) {
    // Centro del ojo en la pantalla
    var caja = ojo.getBoundingClientRect();
    var centroX = caja.left + caja.width / 2;
    var centroY = caja.top + caja.height / 2;

    // Ángulo hacia el ratón (Math.atan2 funciona igual que en Java)
    var angulo = Math.atan2(evento.clientY - centroY, evento.clientX - centroX);

    // Lo máximo que puede moverse la pupila
    // (el ojo recorta lo que sobresale, así que puede llegar al borde)
    destinoX = Math.cos(angulo) * caja.width * 0.22;
    destinoY = Math.sin(angulo) * caja.height * 0.2;
}

// Se ejecuta en cada fotograma: recorre un 25 % de lo que falta hasta el destino
function animar() {
    actualX = actualX + (destinoX - actualX) * 0.25;
    actualY = actualY + (destinoY - actualY) * 0.25;

    pupila.style.transform = 'translate(calc(-50% + ' + actualX + 'px), calc(-50% + ' + actualY + 'px))';

    // Pido al navegador que vuelva a llamar a animar() en el siguiente fotograma
    requestAnimationFrame(animar);
}

// Solo si estoy en la portada (en las demás páginas no hay elefante)
if (portada !== null) {
    portada.addEventListener('mousemove', seguirRaton);
    portada.addEventListener('mouseleave', reposo);

    reposo();
    actualX = destinoX;   // Empieza ya en su sitio, sin moverse al cargar
    animar();
}
