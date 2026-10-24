# Pasos de la práctica

Aquí sigo la práctica en el mismo orden que el enunciado. En cada paso apunto:

- **Qué pide**: lo que dice el enunciado, resumido.
- **Dónde está**: los archivos que lo hacen (cada enlace lleva directo al sitio).
- **Lo que tengo que entender**: lo que me pueden preguntar en la defensa.
- **Cómo lo compruebo**: una prueba rápida para ver que funciona.

Para dudas concretas ("¿dónde está X?", "me sale este error") uso [Helipuerto.md](Helipuerto.md).

---

## Antes de nada: arrancar la aplicación

1. Abro Docker Desktop y espero a que esté en verde.
2. En una terminal, dentro de esta carpeta, ejecuto `docker compose up -d`.
3. Abro http://localhost:8080 → debe salir el tablón con 3 anuncios.
4. Entro con `ana@tablon.test` / `1234` (o `luis@tablon.test` / `1234`).
5. Para ver la base de datos: http://localhost:8081 (root / Tablon_2026).

Si cambio [database.sql](database.sql) tengo que borrar la base de datos y crearla otra vez, porque solo se ejecuta la primera vez: `docker compose down -v` y luego `docker compose up -d`.

---

## Paso 1. Preparar el proyecto

**Qué pide:** Docker con Apache + PHP + MySQL + phpMyAdmin, las carpetas de MVC, Composer con autoload, un `config.php` fuera de Git, una clase `Database` con `query()`, `fetchAll()` y `fetchOne()`, y las tablas `users`, `posts` y `comments`.

**Dónde está:**

1. [docker-compose.yml](docker-compose.yml): los 3 contenedores y qué se ejecuta al arrancar.
2. [Dockerfile](Dockerfile): cómo se monta la imagen de PHP + Apache.
3. [composer.json](composer.json): el autoload PSR-4 (`App\` = carpeta `src/`).
4. [config.example.php](config.example.php#L15): los datos de la BD. El contenedor lo copia a `config.php` al arrancar.
5. [.gitignore](.gitignore): por qué `config.php` y `vendor/` no se suben.
6. [database.sql](database.sql): las 3 tablas, sus claves foráneas y los datos de prueba.
7. [src/Core/Database.php](src/Core/Database.php#L25): la conexión y los 3 métodos.

**Lo que tengo que entender:**

1. Por qué el host es `mysql` y no `localhost` (cada contenedor es como un ordenador distinto).
2. Qué hace el autoload: si uso `App\Core\Database`, PHP busca solo `src/Core/Database.php`.
3. Qué es una sentencia preparada y por qué evita la inyección SQL ([query()](src/Core/Database.php#L56)).
4. Diferencia entre [query()](src/Core/Database.php#L56), [fetchAll()](src/Core/Database.php#L67) y [fetchOne()](src/Core/Database.php#L77).
5. Qué es una clave foránea y qué hace `ON DELETE CASCADE` en la tabla `comments`.
6. Por qué la columna `password` es `VARCHAR(255)` (guarda un hash, no la contraseña).

**Cómo lo compruebo:** en phpMyAdmin veo las 3 tablas con datos, y en `users` la contraseña es un hash que empieza por `$2y$`.

---

## Paso 2. Modelo base

**Qué pide:** una clase abstracta `BaseModel` con `getAll()`, `getById()`, `delete()`, `insert()` y `update()`, para no repetir ese código en cada modelo.

**Dónde está:** [src/Models/BaseModel.php](src/Models/BaseModel.php)

1. [getAll()](src/Models/BaseModel.php#L33)
2. [getById()](src/Models/BaseModel.php#L39)
3. [delete()](src/Models/BaseModel.php#L49): aquí está mi respuesta a la pregunta del enunciado de si va en `Database` o en `BaseModel`.
4. [insert()](src/Models/BaseModel.php#L61)
5. [update()](src/Models/BaseModel.php#L75)

**Lo que tengo que entender:**

1. Por qué es `abstract` (no se puede hacer `new BaseModel()`, solo `new Post()`, `new User()`…).
2. Cómo `insert()` monta el SQL a partir de las claves del array.
3. Por qué los nombres de columna van pegados al SQL y no pasa nada: el array lo monto yo en el controlador, nunca viene directamente del usuario.
4. Cómo cada modelo hijo le dice a `BaseModel` su tabla con `parent::__construct()` (por ejemplo en [User.php](src/Models/User.php#L17)).

**Cómo lo compruebo:** todavía no hay nada que ver en la web; lo compruebo en los pasos siguientes, cuando crear, editar y borrar funcionan.

---

## Paso 3. Usuarios: registro, login y logout

**Qué pide:** el modelo `User` con `findByEmail()`, el `AuthController` con sus 5 acciones, contraseñas con `password_hash()` y en la cabecera el email y "Cerrar sesión" si hay alguien logueado.

**Dónde está:**

1. [src/Models/User.php → findByEmail()](src/Models/User.php#L34) y [emailExists()](src/Models/User.php#L56)
2. [src/Controllers/AuthController.php](src/Controllers/AuthController.php):
   - [register()](src/Controllers/AuthController.php#L31)
   - [login()](src/Controllers/AuthController.php#L77)
   - [logout()](src/Controllers/AuthController.php#L109)
3. [src/Views/auth/register.php](src/Views/auth/register.php) y [login.php](src/Views/auth/login.php)
4. [src/Views/partials/header.php](src/Views/partials/header.php#L36): el menú que cambia según si estoy logueado.
5. [public/index.php](public/index.php#L21): dónde arranca la sesión.

**Lo que tengo que entender:**

1. Por qué no puedo buscar con `WHERE password = password_hash(...)`: el hash lleva una "sal" aleatoria y sale distinto cada vez. Por eso se usa `password_verify()`.
2. Qué guardo en `$_SESSION` al hacer login y para qué sirve después.
3. Para qué sirve `session_regenerate_id(true)`.
4. Por qué el error de login no dice si falla el email o la contraseña.

**Cómo lo compruebo:** casos 1 a 9 de [TESTING.md](TESTING.md).

---

## Paso 4. Anuncios y permisos

**Qué pide:** el modelo `Post` y el `PostController` con `index`, `show`, `create`, `store`, `edit`, `update` y `destroy`; un buscador por título o categoría; que solo los logueados puedan publicar, y que solo el autor pueda editar o borrar.

**Dónde está:**

1. [src/Models/Post.php → search()](src/Models/Post.php#L30): el buscador con `LIKE`.
2. [src/Controllers/PostController.php](src/Controllers/PostController.php):
   - [index()](src/Controllers/PostController.php#L23) y [show()](src/Controllers/PostController.php#L42)
   - [create()](src/Controllers/PostController.php#L59) y [store()](src/Controllers/PostController.php#L69)
   - [edit()](src/Controllers/PostController.php#L90) y [update()](src/Controllers/PostController.php#L104)
   - [destroy()](src/Controllers/PostController.php#L124)
   - [findOwnPost()](src/Controllers/PostController.php#L143): la comprobación de que el anuncio es mío.
   - [readForm()](src/Controllers/PostController.php#L164): la validación de los campos.
3. [src/Core/Controller.php → requireLogin()](src/Core/Controller.php#L68): obliga a estar logueado.
4. Vistas: [index.php](src/Views/posts/index.php), [show.php](src/Views/posts/show.php), [create.php](src/Views/posts/create.php) y [edit.php](src/Views/posts/edit.php).

**Lo que tengo que entender:**

1. **Autenticación** (saber quién eres → `requireLogin()`) frente a **autorización** (si te dejo hacer algo → `findOwnPost()`).
2. Por qué `findOwnPost()` está en `edit`, `update` y `destroy`, aunque en la vista ya escondo los botones con [$isOwner](src/Views/posts/show.php#L21): alguien podría mandar la petición a mano.
3. Por qué el `user_id` del anuncio sale de la sesión y no del formulario.
4. Diferencia entre 403 (no tienes permiso) y 404 (no existe).
5. Por qué buscar va por GET y borrar por POST.
6. Para qué sirve `htmlspecialchars()` en las vistas (evita XSS).

**Cómo lo compruebo:** casos 10 a 20 y 25 a 29 de [TESTING.md](TESTING.md). El más importante: entrar como ana e intentar editar un anuncio de luis cambiando el id en la URL → tiene que salir el error 403.

---

## Paso 5. Comentarios (relación 1:N)

**Qué pide:** el modelo `Comment` y un `CommentController`; mostrar los comentarios dentro del detalle del anuncio, ordenados por fecha, y el formulario para comentar solo si estoy logueado.

**Dónde está:**

1. [src/Models/Comment.php → getByPost()](src/Models/Comment.php#L25)
2. [src/Controllers/CommentController.php → store()](src/Controllers/CommentController.php#L23)
3. [src/Views/posts/show.php](src/Views/posts/show.php#L51): la lista de comentarios y el formulario.

**Lo que tengo que entender:**

1. Qué significa 1:N: un anuncio tiene muchos comentarios, y cada comentario es de un solo anuncio.
2. Por qué el `user_id` del comentario sale de la sesión y nunca de un campo oculto.
3. Por qué el `post_id` sí puede venir de un campo oculto (cambiarlo solo haría comentar en otro anuncio).
4. Por qué `CommentController` solo necesita `store()`.
5. Qué hace el `JOIN` con `users` (sacar el nombre de quien comenta).

**Cómo lo compruebo:** casos 21 a 24 de [TESTING.md](TESTING.md), y borrar un anuncio que tenga comentarios (caso 19).

---

## Paso 6. Token CSRF

**Qué pide:** un token por sesión, oculto en todos los formularios POST (también login y registro), y comprobarlo con `hash_equals()` antes de hacer nada.

**Dónde está:**

1. [public/index.php](public/index.php#L26): dónde se crea el token.
2. [src/Core/Controller.php → checkCsrf()](src/Core/Controller.php#L36): dónde se comprueba.
3. El campo oculto `csrf_token` en cada formulario, por ejemplo en [login.php](src/Views/auth/login.php#L14) o en [show.php](src/Views/posts/show.php#L71).

**Lo que tengo que entender:**

1. Qué es un ataque CSRF (otra web me hace enviar un formulario sin que me dé cuenta).
2. Por qué `checkCsrf()` va lo primero en cada acción POST.
3. Por qué se compara con `hash_equals()` y no con `==`.
4. Por qué devuelvo 403 y no 419 (Apache no conoce el 419 y lo cambia por 500).

**Cómo lo compruebo:** casos 30 y 31 de [TESTING.md](TESTING.md): borro el campo oculto con F12, envío el formulario y tiene que salir "Token CSRF inválido".

---

## Paso 7. Rutas limpias (opcional)

**Qué pide:** que el id vaya dentro de la URL: `/posts/delete/3` en vez de `/posts/delete` con el id en un campo oculto, y que el enrutador llame a `destroy(3)`. Que sirva para cualquier controlador, acción e id. Sube hasta +1 punto aparte.

**Estado:** hecho.

**Dónde está:**

1. [src/Core/Router.php → dispatch()](src/Core/Router.php#L39): convierte cada ruta en una expresión regular (`{id}` → `(\d+)`) y, si la URL encaja, llama al método pasándole el número.
2. [public/index.php → rutas](public/index.php#L31): las rutas con su hueco `{id}`, por ejemplo `'/posts/delete/{id}'`.
3. [PostController.php](src/Controllers/PostController.php#L42): `show`, `edit`, `update` y `destroy` reciben el id como parámetro (`int $id`), ya no lo leen de `$_GET` ni `$_POST`.

| Antes | Ahora |
|---|---|
| `/posts/show?id=3` | `/posts/3` |
| `/posts/edit?id=3` | `/posts/edit/3` |
| POST `/posts/update` + id oculto | POST `/posts/update/3` |
| POST `/posts/delete` + id oculto | POST `/posts/delete/3` → `destroy(3)` |

**Lo que tengo que entender:**

1. Qué es una expresión regular: un patrón de texto. `(\d+)` significa "uno o más dígitos" y los paréntesis guardan lo que encaja en `$matches[1]`.
2. Por qué `/posts/abc` da 404: `(\d+)` solo acepta números, así que no encaja con ninguna ruta.
3. Por qué `/posts/create` no se confunde con `/posts/{id}`: "create" no son dígitos.
4. Que el id de la URL lo puede cambiar cualquiera: por eso `findOwnPost()` sigue comprobando que el anuncio sea tuyo.

**Cómo lo compruebo:** casos 32 a 35 de [TESTING.md](TESTING.md).

---

## Extra. Diseño: CSS y un poco de JavaScript

**Qué es:** no lo pide el enunciado. Es para que la web tenga buena pinta. No cambia nada de cómo funciona el PHP: solo toca las vistas (el HTML), el CSS y dos archivos de JavaScript.

**Dónde está:**

1. [public/css/style.css](public/css/style.css): todos los estilos. Los colores están arriba, en `:root`, sacados del logo de PHP.
2. [header.php](src/Views/partials/header.php#L22): enlaza el CSS, las letras (Google Fonts) y el icono de la pestaña.
3. [public/img/](public/img): el elefante de PHP (`elefante-php.webp`) y el icono de archivo PHP (`php-archivo.svg`).
4. [Views/posts/index.php](src/Views/posts/index.php#L37): la portada con el elefante y un ojo encima hecho con HTML.
5. [public/js/ojo-elefante.js](public/js/ojo-elefante.js): hace que la pupila del elefante mire al ratón.
6. [footer.php](src/Views/partials/footer.php) y [public/js/carga.js](public/js/carga.js): la pantalla de carga con el elefante corriendo.

**Lo que tengo que entender:**

1. Por qué el CSS, el JS y las imágenes van en `public/`: es lo único que el navegador puede pedir directamente.
2. Que el CSS solo cambia cómo se ve. Si lo quito, la web funciona igual, pero fea.
3. Que el JavaScript se ejecuta en el **navegador**, no en el servidor como el PHP.
4. Cuándo sale la pantalla de carga: si una página tarda más de 400 ms, y siempre durante 5 segundos al publicar un anuncio.

**Cómo lo compruebo:** muevo el ratón por la cabecera del tablón y el ojo del elefante lo sigue. Publico un anuncio y sale el elefante corriendo antes de volver al listado.

---

## Paso 8. Plan de pruebas

**Qué pide:** un `TESTING.md` con casos de prueba hechos con ayuda de IA, ejecutados a mano en la aplicación, apuntando el resultado y si pasa o no.

**Dónde está:** [TESTING.md](TESTING.md)

**Lo que tengo que hacer:** probar los 35 casos uno a uno y rellenar "Resultado" y "¿Pasa?" en cada uno.

---

## Lo que me queda para entregar

- [x] Rellenar [TESTING.md](TESTING.md) probando cada caso (35 de 35 pasan).
- [x] Subir a GitHub cada paso con su commit.
- [x] Comprobar que arranca de cero en otro equipo con `docker compose up -d` (probado descargando el repositorio en una carpeta nueva).
- [ ] Vídeo: el que tengo está grabado antes de añadir el diseño (CSS). Funciona igual, pero se ve distinto: decidir si lo vuelvo a grabar.
- [ ] Exportar toda la conversación con la IA a un `.docx` o `.odt`.
- [ ] Comprimir el código fuente (con TESTING.md) y subirlo a Moodle con el vídeo, el documento y el enlace al repositorio.
- [ ] Antes de entregar, que el profe pueda ver el repositorio (hacerlo público o invitarle).
