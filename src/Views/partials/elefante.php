<?php
/* #####################################################################
 *  ELEFANTE DE PHP ANIMADO (lo incluye posts/index.php en la portada)
 *  Es un dibujo SVG hecho a mano, separado en piezas para poder
 *  animarlas desde style.css:
 *   .cola    -> se mueve de lado a lado
 *   .pupila  -> mira a un lado y a otro
 *   .parpado -> baja y sube de vez en cuando (parpadeo)
 *  El elefante en sí está quieto: solo se mueven esas piezas.
 * ##################################################################### */
?>
<svg class="elefante" viewBox="0 0 280 190" role="img" aria-label="Elefante de PHP">
    <defs>
        <!-- Degradado lila del cuerpo, como el logo original -->
        <linearGradient id="piel" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#a3a9dc"/>
            <stop offset="1" stop-color="#6c72b0"/>
        </linearGradient>
    </defs>

    <g stroke="#2e2a4f" stroke-width="5" stroke-linejoin="round">
        <!-- Cola: va detrás del cuerpo y gira desde donde se une a él -->
        <g class="cola">
            <path d="M234 60 Q252 50 262 58" fill="none" stroke-width="6" stroke-linecap="round"/>
            <path d="M258 50 q16 -2 16 10 q-4 12 -16 6 q-6 -8 0 -16 z" fill="#8a90c8" stroke-width="3.5"/>
        </g>

        <!-- Patas de atrás y del medio -->
        <rect x="186" y="110" width="36" height="66" rx="12" fill="url(#piel)"/>
        <rect x="128" y="110" width="36" height="66" rx="12" fill="url(#piel)"/>

        <!-- Cuerpo -->
        <rect x="96" y="22" width="142" height="122" rx="42" fill="url(#piel)"/>

        <!-- Pata de delante (queda debajo de la cabeza) -->
        <rect x="70" y="110" width="36" height="66" rx="12" fill="url(#piel)"/>

        <!-- Trompa: un borde oscuro grueso y encima el relleno lila -->
        <path d="M48 88 Q20 128 28 158 Q34 176 52 166" fill="none" stroke-width="36" stroke-linecap="round"/>
        <path d="M48 88 Q20 128 28 158 Q34 176 52 166" fill="none" stroke="#8d93cb" stroke-width="26" stroke-linecap="round"/>

        <!-- Cabeza -->
        <ellipse cx="80" cy="70" rx="58" ry="54" fill="url(#piel)"/>

        <!-- Oreja -->
        <path d="M108 34 Q150 40 146 84 Q142 118 112 112 Q98 80 108 34 z" fill="#8d93cb"/>
    </g>

    <!-- Brillo de la cabeza -->
    <ellipse cx="60" cy="38" rx="26" ry="10" fill="#fff" opacity="0.3"/>

    <!-- Uñas de las patas -->
    <g fill="#c3c8ee" stroke="#2e2a4f" stroke-width="2.5">
        <ellipse cx="80" cy="172" rx="6" ry="8"/>
        <ellipse cx="94" cy="172" rx="6" ry="8"/>
        <ellipse cx="138" cy="172" rx="6" ry="8"/>
        <ellipse cx="152" cy="172" rx="6" ry="8"/>
        <ellipse cx="196" cy="172" rx="6" ry="8"/>
        <ellipse cx="210" cy="172" rx="6" ry="8"/>
    </g>

    <!-- "php" escrito en el cuerpo -->
    <text x="188" y="98" text-anchor="middle" class="elefante-texto">php</text>

    <!-- Cara: ceja, sonrisa y ojo -->
    <g fill="none" stroke="#2e2a4f" stroke-width="4" stroke-linecap="round">
        <path d="M44 44 Q58 34 74 40"/>
        <path d="M66 92 Q80 102 94 90"/>
    </g>

    <g class="ojo">
        <ellipse cx="60" cy="62" rx="11" ry="12" fill="#fff" stroke="#2e2a4f" stroke-width="3"/>
        <!-- La pupila con su brillito: es lo que se mueve -->
        <g class="pupila">
            <circle cx="62" cy="63" r="5.5" fill="#2e2a4f"/>
            <circle cx="64" cy="61" r="1.8" fill="#fff"/>
        </g>
        <!-- El párpado tapa el ojo: normalmente está "plegado" (escala 0) -->
        <ellipse class="parpado" cx="60" cy="62" rx="12" ry="13" fill="#959bd2" stroke="#2e2a4f" stroke-width="3"/>
    </g>
</svg>
