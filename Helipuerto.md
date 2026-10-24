# Helipuerto

Mis apuntes de dónde mirar según lo que quiera hacer o la duda que tenga. Cada enlace lleva directo al archivo o a la línea.

## Arrancar y Docker

1. **Arrancar o parar la aplicación**: [README.md](README.md)
2. **Qué contenedores hay y qué pasa al arrancar**: [docker-compose.yml](docker-compose.yml)
3. **Cómo se monta Apache + PHP**: [Dockerfile](Dockerfile)
4. **Usuario y contraseña de la base de datos**: [config.example.php](config.example.php#L15). El `config.php` se crea solo al arrancar a partir de este.
5. **Ver la base de datos a mano**: phpMyAdmin en http://localhost:8081 (root / Tablon_2026)

## Base de datos

6. **Tablas, columnas, claves foráneas y datos de prueba**: [database.sql](database.sql)
7. **Cómo se conecta PHP a MySQL**: [Database.php → constructor](src/Core/Database.php#L25)
8. **Cómo se lanza cualquier consulta**:
   - [Database.php → query()](src/Core/Database.php#L56)
   - [Database.php → fetchAll()](src/Core/Database.php#L67)
   - [Database.php → fetchOne()](src/Core/Database.php#L77)
9. **Obtener todos, uno por id, insertar, modificar o borrar** (sirve para todas las tablas):
   - [BaseModel.php → getAll()](src/Models/BaseModel.php#L33)
   - [BaseModel.php → getById()](src/Models/BaseModel.php#L39)
   - [BaseModel.php → delete()](src/Models/BaseModel.php#L49)
   - [BaseModel.php → insert()](src/Models/BaseModel.php#L61)
   - [BaseModel.php → update()](src/Models/BaseModel.php#L75)
10. **Consultas especiales con JOIN o LIKE**:
    - [Post.php → search()](src/Models/Post.php#L30)
    - [Post.php → getByIdWithAuthor()](src/Models/Post.php#L45)
    - [Comment.php → getByPost()](src/Models/Comment.php#L25)
    - [User.php → findByEmail()](src/Models/User.php#L34)
    - [User.php → emailExists()](src/Models/User.php#L56)

## Cómo viaja una petición

11. **Qué URL ejecuta qué método** (la lista de rutas): [index.php → rutas](public/index.php#L31)
12. **Cómo se elige y se llama al controlador**: [Router.php → dispatch()](src/Core/Router.php#L32)
13. **Por qué todo pasa por index.php**: [public/.htaccess](public/.htaccess)

Si quiero **añadir una página nueva**, toco siempre tres sitios, en este orden:

1. Una ruta en [public/index.php](public/index.php#L31)
2. Un método en el controlador que toque (carpeta `src/Controllers/`)
3. Un archivo de vista en `src/Views/`

## Usuarios

14. **Registro, login y logout**:
    - [AuthController.php → register()](src/Controllers/AuthController.php#L31)
    - [AuthController.php → login()](src/Controllers/AuthController.php#L77)
    - [AuthController.php → logout()](src/Controllers/AuthController.php#L109)
15. **Cómo se comprueba la contraseña**: [User.php → findByEmail()](src/Models/User.php#L34)
16. **Dónde se arranca la sesión**: [index.php → session_start()](public/index.php#L21)
17. **Qué se guarda al loguearse**: [AuthController.php → login()](src/Controllers/AuthController.php#L100)
18. **Los formularios de login y registro**:
    - [Views/auth/login.php](src/Views/auth/login.php)
    - [Views/auth/register.php](src/Views/auth/register.php)
19. **El menú que cambia según si estás logueado**: [Views/partials/header.php](src/Views/partials/header.php#L33)

## Anuncios y permisos

20. **Listar, ver, crear, editar y borrar anuncios**:
    - [PostController.php → index()](src/Controllers/PostController.php#L23)
    - [PostController.php → show()](src/Controllers/PostController.php#L41)
    - [PostController.php → create()](src/Controllers/PostController.php#L61)
    - [PostController.php → store()](src/Controllers/PostController.php#L71)
    - [PostController.php → edit()](src/Controllers/PostController.php#L92)
    - [PostController.php → update()](src/Controllers/PostController.php#L107)
    - [PostController.php → destroy()](src/Controllers/PostController.php#L128)
21. **Obligar a estar logueado** (autenticación): [Controller.php → requireLogin()](src/Core/Controller.php#L68)
22. **Que solo el autor pueda editar o borrar** (autorización): [PostController.php → findOwnPost()](src/Controllers/PostController.php#L148)
23. **Validar que los campos no estén vacíos**: [PostController.php → readForm()](src/Controllers/PostController.php#L169)
24. **Esconder los botones de Editar y Borrar si no es tuyo**: [show.php → $isOwner](src/Views/posts/show.php#L21)
25. **Los formularios de crear y editar**:
    - [Views/posts/create.php](src/Views/posts/create.php#L12)
    - [Views/posts/edit.php](src/Views/posts/edit.php#L14)

## Buscador

26. **La consulta con LIKE**: [Post.php → search()](src/Models/Post.php#L30)
27. **Dónde se lee lo que se ha buscado**: [PostController.php → index()](src/Controllers/PostController.php#L23)
28. **La caja de búsqueda**: [Views/posts/index.php](src/Views/posts/index.php#L25)

## Comentarios

29. **Guardar un comentario**: [CommentController.php → store()](src/Controllers/CommentController.php#L23)
30. **Sacar los comentarios de un anuncio**: [Comment.php → getByPost()](src/Models/Comment.php#L25)
31. **Dónde se pintan y el formulario para comentar**: [show.php → comentarios](src/Views/posts/show.php#L51)

## Seguridad

32. **Dónde se crea el token CSRF**: [index.php → token CSRF](public/index.php#L26)
33. **Dónde se comprueba**: [Controller.php → checkCsrf()](src/Core/Controller.php#L36)
34. **Dónde va en los formularios**: en cada vista con `<form method="post">`, el campo oculto `csrf_token`. Por ejemplo, en [login.php](src/Views/auth/login.php#L14) o en [show.php](src/Views/posts/show.php#L71).
35. **Inyección SQL**: [Database.php → query()](src/Core/Database.php#L56) (prepare + execute)
36. **XSS**: en todas las vistas, cada `htmlspecialchars(...)`. Por ejemplo, en [Views/posts/show.php](src/Views/posts/show.php).

## Mensajes y errores de la aplicación

37. **Los mensajes verdes y rojos**:
    - Se guardan en [Controller.php → redirect()](src/Core/Controller.php#L54)
    - Se pintan en [header.php → mensajes](src/Views/partials/header.php#L46)
38. **Las páginas de error 404 y 403**:
    - [Controller.php → error()](src/Core/Controller.php#L80)
    - [Views/error.php](src/Views/error.php)

## Diseño: CSS, imágenes y JavaScript

39. **Todos los estilos (colores, letras, botones, tabla...)**: [public/css/style.css](public/css/style.css). Los colores están arriba del todo, en `:root`: si cambio uno ahí, cambia en toda la web.
40. **Dónde se enlazan el CSS, las letras de Google Fonts y el icono de la pestaña**: [header.php](src/Views/partials/header.php#L22)
41. **Las imágenes**: [public/img/](public/img) → `elefante-php.webp` (el elefante de la portada), `elefante-corriendo.webp` (el elefante corriendo de la pantalla de carga: es el GIF oficial de php.net, de 78x48 px, ampliado con IA (Real-ESRGAN) a 312x192) y `php-archivo.svg` (el icono del menú y de la pestaña).
42. **El elefante de la portada y su ojo**: [Views/posts/index.php](src/Views/posts/index.php#L37). Encima del ojo del dibujo hay otro ojo hecho con HTML.
43. **Que el ojo siga al ratón**: [public/js/ojo-elefante.js](public/js/ojo-elefante.js)
44. **La pantalla de carga con el elefante corriendo**:
    - El HTML está en [footer.php](src/Views/partials/footer.php), así sale en todas las páginas.
    - Cuándo sale: [public/js/carga.js](public/js/carga.js) (si una página tarda más de 400 ms, y siempre 4,5 segundos al publicar un anuncio).
45. **¿Por qué el CSS, el JS y las imágenes van en `public/`?** Porque es la única carpeta que el navegador puede pedir. El [.htaccess](public/.htaccess) deja pasar los archivos que existen y todo lo demás lo manda a `index.php`.

## Si me sale un error

46. **Pantalla en blanco o error 500**: ejecuto `docker compose logs web` y miro las últimas líneas.
47. **"404 - Página no encontrada"**: la URL no está en las [rutas de index.php](public/index.php#L31), o el verbo no coincide (GET o POST).
48. **"Token CSRF inválido"**: al formulario le falta el campo oculto `csrf_token`.
49. **"Class ... not found"**: el `namespace` no coincide con la carpeta, o falta un `use`. Si he creado una clase nueva, ejecuto `docker compose restart web` para que se regenere el autoload.
50. **Error de conexión a la base de datos (SQLSTATE)**: reviso el host y la contraseña en `config.php` (copia de [config.example.php](config.example.php#L15)).
51. **"Unknown column"**: el nombre de la columna en el modelo o el controlador no coincide con [database.sql](database.sql).
52. **He cambiado database.sql y no se nota**: solo se ejecuta la primera vez. Hago `docker compose down -v` y después `docker compose up -d`.
53. **No sé qué probar**: [TESTING.md](TESTING.md)
54. **No sé en qué orden leer el código**: [PASOS.md](PASOS.md)
