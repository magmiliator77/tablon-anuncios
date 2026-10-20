# Helipuerto

Mis apuntes de dónde mirar según lo que quiera hacer o la duda que tenga. Cada enlace lleva directo al archivo o a la línea.

## Arrancar y Docker

1. **Arrancar o parar la aplicación**: [README.md](README.md)
2. **Qué contenedores hay y qué pasa al arrancar**: [docker-compose.yml](docker-compose.yml)
3. **Cómo se monta Apache + PHP**: [Dockerfile](Dockerfile)
4. **Usuario y contraseña de la base de datos**: [config.example.php](config.example.php#L15). El `config.php` se crea solo al arrancar a partir de este.
5. **Ver la base de datos a mano**: phpMyAdmin en http://localhost:8081 (root / root)

## Base de datos

6. **Tablas, columnas, claves foráneas y datos de prueba**: [database.sql](database.sql)
7. **Cómo se conecta PHP a MySQL**: [Database.php → constructor](src/Core/Database.php#L25)
8. **Cómo se lanza cualquier consulta**:
   - [Database.php → query()](src/Core/Database.php#L56)
   - [Database.php → fetchAll()](src/Core/Database.php#L67)
   - [Database.php → fetchOne()](src/Core/Database.php#L77)
9. **Obtener todos, uno por id, insertar, modificar o borrar** (sirve para todas las tablas):
   - [BaseModel.php → getAll()](src/Models/BaseModel.php#L29)
   - [BaseModel.php → getById()](src/Models/BaseModel.php#L35)
   - [BaseModel.php → delete()](src/Models/BaseModel.php#L45)
   - [BaseModel.php → insert()](src/Models/BaseModel.php#L57)
   - [BaseModel.php → update()](src/Models/BaseModel.php#L71)
10. **Consultas especiales con JOIN o LIKE**:
    - [Post.php → search()](src/Models/Post.php#L30)
    - [Post.php → getByIdWithAuthor()](src/Models/Post.php#L45)
    - [Comment.php → getByPost()](src/Models/Comment.php#L25)
    - [User.php → findByEmail()](src/Models/User.php#L34)
    - [User.php → emailExists()](src/Models/User.php#L48)

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
    - [AuthController.php → register()](src/Controllers/AuthController.php#L25)
    - [AuthController.php → login()](src/Controllers/AuthController.php#L71)
    - [AuthController.php → logout()](src/Controllers/AuthController.php#L102)
15. **Cómo se comprueba la contraseña**: [User.php → findByEmail()](src/Models/User.php#L34)
16. **Dónde se arranca la sesión**: [index.php → session_start()](public/index.php#L21)
17. **Qué se guarda al loguearse**: [AuthController.php → login()](src/Controllers/AuthController.php#L86)
18. **Los formularios de login y registro**:
    - [Views/auth/login.php](src/Views/auth/login.php)
    - [Views/auth/register.php](src/Views/auth/register.php)
19. **El menú que cambia según si estás logueado**: [Views/partials/header.php](src/Views/partials/header.php#L25)

## Anuncios y permisos

20. **Listar, ver, crear, editar y borrar anuncios**:
    - [PostController.php → index()](src/Controllers/PostController.php#L23)
    - [PostController.php → show()](src/Controllers/PostController.php#L40)
    - [PostController.php → create()](src/Controllers/PostController.php#L58)
    - [PostController.php → store()](src/Controllers/PostController.php#L68)
    - [PostController.php → edit()](src/Controllers/PostController.php#L88)
    - [PostController.php → update()](src/Controllers/PostController.php#L103)
    - [PostController.php → destroy()](src/Controllers/PostController.php#L123)
21. **Obligar a estar logueado** (autenticación): [Controller.php → requireLogin()](src/Core/Controller.php#L67)
22. **Que solo el autor pueda editar o borrar** (autorización): [PostController.php → findOwnPost()](src/Controllers/PostController.php#L142)
23. **Validar que los campos no estén vacíos**: [PostController.php → readForm()](src/Controllers/PostController.php#L162)
24. **Esconder los botones de Editar y Borrar si no es tuyo**: [show.php → $isOwner](src/Views/posts/show.php#L20)
25. **Los formularios de crear y editar**:
    - [Views/posts/create.php](src/Views/posts/create.php#L12)
    - [Views/posts/edit.php](src/Views/posts/edit.php#L14)

## Buscador

26. **La consulta con LIKE**: [Post.php → search()](src/Models/Post.php#L30)
27. **Dónde se lee lo que se ha buscado**: [PostController.php → index()](src/Controllers/PostController.php#L23)
28. **La caja de búsqueda**: [Views/posts/index.php](src/Views/posts/index.php#L21)

## Comentarios

29. **Guardar un comentario**: [CommentController.php → store()](src/Controllers/CommentController.php#L23)
30. **Sacar los comentarios de un anuncio**: [Comment.php → getByPost()](src/Models/Comment.php#L25)
31. **Dónde se pintan y el formulario para comentar**: [show.php → comentarios](src/Views/posts/show.php#L48)

## Seguridad

32. **Dónde se crea el token CSRF**: [index.php → token CSRF](public/index.php#L26)
33. **Dónde se comprueba**: [Controller.php → checkCsrf()](src/Core/Controller.php#L36)
34. **Dónde va en los formularios**: en cada vista con `<form method="post">`, el campo oculto `csrf_token`. Por ejemplo, en [login.php](src/Views/auth/login.php#L10) o en [show.php](src/Views/posts/show.php#L67).
35. **Inyección SQL**: [Database.php → query()](src/Core/Database.php#L56) (prepare + execute)
36. **XSS**: en todas las vistas, cada `htmlspecialchars(...)`. Por ejemplo, en [Views/posts/show.php](src/Views/posts/show.php).

## Mensajes y errores de la aplicación

37. **Los mensajes verdes y rojos**:
    - Se guardan en [Controller.php → redirect()](src/Core/Controller.php#L53)
    - Se pintan en [header.php → mensajes](src/Views/partials/header.php#L39)
38. **Las páginas de error 404 y 403**:
    - [Controller.php → error()](src/Core/Controller.php#L78)
    - [Views/error.php](src/Views/error.php)

## Si me sale un error

39. **Pantalla en blanco o error 500**: ejecuto `docker compose logs web` y miro las últimas líneas.
40. **"404 - Página no encontrada"**: la URL no está en las [rutas de index.php](public/index.php#L31), o el verbo no coincide (GET o POST).
41. **"Token CSRF inválido"**: al formulario le falta el campo oculto `csrf_token`.
42. **"Class ... not found"**: el `namespace` no coincide con la carpeta, o falta un `use`. Si he creado una clase nueva, ejecuto `docker compose restart web` para que se regenere el autoload.
43. **Error de conexión a la base de datos (SQLSTATE)**: reviso el host y la contraseña en `config.php` (copia de [config.example.php](config.example.php#L15)).
44. **"Unknown column"**: el nombre de la columna en el modelo o el controlador no coincide con [database.sql](database.sql).
45. **He cambiado database.sql y no se nota**: solo se ejecuta la primera vez. Hago `docker compose down -v` y después `docker compose up -d`.
46. **No sé qué probar**: [TESTING.md](TESTING.md)
47. **No sé en qué orden leer el código**: [PASOS.md](PASOS.md)
