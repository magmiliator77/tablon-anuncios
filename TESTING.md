# Plan de pruebas

He generado estos casos con ayuda de IA y los he ejecutado contra la aplicación arrancada con Docker, empezando con la base de datos limpia (`docker compose down -v` y `docker compose up -d`). En cada uno apunto lo que pasó de verdad y si pasa la prueba.

En los casos que dicen "quitando con F12", la prueba consiste en enviar el formulario sin ese campo, que es lo mismo que pasa si se borra en el navegador.

Usuarios de prueba: `ana@tablon.test` / `1234` y `luis@tablon.test` / `1234`.

**Resumen: 31 de 31 casos pasan.**

## Usuarios

1. Me registro con datos correctos
   - Debería: ir al login con "Cuenta creada"
   - Resultado: redirige a `/login` y sale en verde "Cuenta creada. Ya puedes iniciar sesión."
   - ¿Pasa?: Sí

2. Me registro con un email que ya existe
   - Debería: volver al registro con "Ya existe un usuario con ese email"
   - Resultado: con `ana@tablon.test` vuelve a `/register` y sale en rojo "Ya existe un usuario con ese email."
   - ¿Pasa?: Sí

3. Me registro dejando la contraseña vacía (quitando `required` con F12)
   - Debería: salir "Rellena todos los campos"
   - Resultado: vuelve a `/register` con "Rellena todos los campos." en rojo. No se crea el usuario.
   - ¿Pasa?: Sí

4. Me registro con un email mal escrito (quitando `type="email"` con F12)
   - Debería: salir "El email no es válido"
   - Resultado: con `esto-no-es-email` vuelve a `/register` con "El email no es válido." en rojo.
   - ¿Pasa?: Sí

5. Hago login con contraseña incorrecta
   - Debería: salir "Email o contraseña incorrectos"
   - Resultado: vuelve a `/login` con "Email o contraseña incorrectos." en rojo.
   - ¿Pasa?: Sí

6. Hago login con un email que no existe
   - Debería: salir el mismo mensaje que en el caso 5
   - Resultado: con `nadie@tablon.test` sale exactamente el mismo mensaje, así que no se sabe si el email existe.
   - ¿Pasa?: Sí

7. Hago login correcto
   - Debería: ir al tablón y arriba salir mi email y "Cerrar sesión"
   - Resultado: redirige a `/posts` con "Has iniciado sesión." y en el menú sale "👤 ana@tablon.test" y "Cerrar sesión".
   - ¿Pasa?: Sí

8. Pulso "Cerrar sesión"
   - Debería: volver a salir arriba "Iniciar sesión"
   - Resultado: redirige a `/posts` y el menú vuelve a mostrar "Iniciar sesión" y "Registrarse".
   - ¿Pasa?: Sí

9. Miro en phpMyAdmin la tabla `users`
   - Debería: la contraseña ser un hash (`$2y$...`), no `1234`
   - Resultado: las tres contraseñas (ana, luis y el usuario nuevo del caso 1) empiezan por `$2y$10$` y tienen 60 caracteres. Ninguna está en texto plano.
   - ¿Pasa?: Sí

## Anuncios

10. Sin sesión, entro a `/posts/create`
    - Debería: mandarme al login con "Tienes que iniciar sesión"
    - Resultado: redirige a `/login` con "Tienes que iniciar sesión para hacer eso." en rojo.
    - ¿Pasa?: Sí

11. Con sesión, publico un anuncio correcto
    - Debería: salir en el listado con "Anuncio publicado"
    - Resultado: "Vendo bici" (texto "Bici de montaña", categoría Venta) sale el primero del listado con "Anuncio publicado." en verde. La ñ se guarda bien.
    - ¿Pasa?: Sí

12. Publico un anuncio con el título vacío (quitando `required` con F12)
    - Debería: volver al formulario con un error
    - Resultado: vuelve a `/posts/create` con "Título, texto y categoría son obligatorios." en rojo. No se guarda.
    - ¿Pasa?: Sí

13. Publico un anuncio con título `<script>alert(1)</script>`
    - Debería: verse el texto tal cual, sin que salte ninguna alerta
    - Resultado: en el HTML del listado aparece como `&lt;script&gt;alert(1)&lt;/script&gt;`, así que se ve como texto y no se ejecuta.
    - ¿Pasa?: Sí

14. Edito un anuncio mío
    - Debería: salir el formulario relleno y guardarse los cambios
    - Resultado: el formulario sale con el título "Vendo libro de Entorno Servidor" ya puesto. Al cambiarlo, redirige al detalle con "Anuncio actualizado." y en la base de datos queda el título nuevo.
    - ¿Pasa?: Sí

15. Siendo ana, abro en el navegador la URL de editar un anuncio de luis
    - Debería: salir error 403 "No tienes permiso"
    - Resultado: `/posts/edit?id=2` devuelve código 403 y la página "No tienes permiso para hacer esto: el anuncio no es tuyo."
    - ¿Pasa?: Sí

16. Siendo ana, veo un anuncio de luis
    - Debería: no salir los botones de Editar ni Borrar
    - Resultado: en el detalle del anuncio 2 no aparece ni "Editar" ni "Borrar".
    - ¿Pasa?: Sí

17. Siendo ana, cambio con F12 el `id` oculto del formulario de borrar por el de un anuncio de luis
    - Debería: salir error 403 y no borrarse
    - Resultado: al enviar el borrado con `id=3` (de luis) devuelve 403, y el anuncio 3 sigue en la base de datos.
    - ¿Pasa?: Sí

18. Borro un anuncio mío sin comentarios
    - Debería: desaparecer del listado
    - Resultado: redirige a `/posts` con "Anuncio eliminado." y ya no está en la base de datos.
    - ¿Pasa?: Sí

19. Borro un anuncio mío que tiene comentarios
    - Debería: desaparecer del listado (y sus comentarios con él)
    - Resultado: el anuncio 1 tenía 2 comentarios. Se borra sin errores y en la base de datos quedan 0 comentarios suyos (por el `ON DELETE CASCADE`).
    - ¿Pasa?: Sí

20. Abro `/posts/show?id=999`
    - Debería: salir error 404 "Ese anuncio no existe"
    - Resultado: devuelve código 404 con "Ese anuncio no existe."
    - ¿Pasa?: Sí

## Comentarios

21. Sin sesión, veo un anuncio
    - Debería: verse los comentarios pero no el formulario
    - Resultado: no aparece el formulario de comentar; en su lugar sale "Inicia sesión para comentar".
    - ¿Pasa?: Sí

22. Con sesión, comento
    - Debería: salir abajo del todo, con mi nombre
    - Resultado: redirige al detalle con "Comentario publicado." y el comentario sale abajo con el nombre "ana".
    - ¿Pasa?: Sí

23. Comento solo con espacios
    - Debería: salir "El comentario no puede estar vacío"
    - Resultado: vuelve al detalle con "El comentario no puede estar vacío." en rojo. No se guarda.
    - ¿Pasa?: Sí

24. Hay varios comentarios
    - Debería: salir del más antiguo al más nuevo
    - Resultado: comenta primero ana y después luis, y salen en ese orden: ana, luis.
    - ¿Pasa?: Sí

## Buscador

25. Busco `venta`
    - Debería: salir los anuncios de categoría Venta
    - Resultado: sale solo "Vendo bici", el único anuncio de categoría Venta que quedaba.
    - ¿Pasa?: Sí

26. Busco una palabra del título
    - Debería: salir ese anuncio
    - Resultado: con `cargador` sale "Cargador olvidado en el aula 21".
    - ¿Pasa?: Sí

27. Busco algo que no existe
    - Debería: salir "No hay anuncios"
    - Resultado: con `zzzz` sale "No hay anuncios".
    - ¿Pasa?: Sí

28. Busco `' OR 1=1 --`
    - Debería: salir "No hay anuncios" (no hay inyección SQL)
    - Resultado: sale "No hay anuncios". El texto se busca tal cual y no cambia la consulta.
    - ¿Pasa?: Sí

29. Después de buscar
    - Debería: seguir escrito el texto buscado en la caja
    - Resultado: después de buscar `cargador`, la caja sigue mostrando `cargador`.
    - ¿Pasa?: Sí

## Seguridad CSRF

30. Borro con F12 el campo oculto `csrf_token` de un formulario y lo envío
    - Debería: salir error 403 "Token CSRF inválido" y no guardarse nada
    - Resultado: devuelve 403 con "Token CSRF inválido. Recarga la página e inténtalo de nuevo." Había 2 anuncios antes y siguen siendo 2.
    - ¿Pasa?: Sí

31. Cambio el valor de `csrf_token` por otro cualquiera
    - Debería: salir error 403
    - Resultado: con `csrf_token=abc123` devuelve 403 y no se crea ningún anuncio.
    - ¿Pasa?: Sí
