## Proyecto Final - API Ecommerce - Laravel

Actividad grupal asignado de parte del bootcamp de Full Stack Jr. de la Academia de Kodigo.

<!-- #### Integrantes:

- -->

### Opción seleccionada (2): API de Gestión de Productos.

- Desarrollar una API para gestionar productos, permitiendo a los usuarios agregar, ver, actualizar y eliminar productos.
  Implementar autenticación utilizando un token de acceso para el usuario.

- Al igual que en la opción 1, el token debe tener una expiración configurada para refrescarse periódicamente.

- Incluir una sección donde los usuarios puedan dejar valoraciones y comentarios sobre los productos, lo que agregará una capa de interacción adicional.

- Calcular el promedio de valoraciones por producto y mostrar el producto con la mejor valoración para ayudar a los usuarios a tomar decisiones informadas.

### Instrucciones de instalación:

1. Clonar el repositorio en tu máquina local.

2. Copiar .env.example a .env y modificar los valores de la base de datos y de la clave de encriptación.

3. Instalar dependencias con Composer. No es necesario instalar los paquetes de NodeJs, solo es una API REST.

```
composer install
```

4. Crear la llave secreta de JWT y llave de la aplicación.

Asignar el valor en .env

```
php artisan jwt:secret
```

```
php artisan key:generate
```

(Opcional):

```
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

5. Ejecutar las migraciones

```
php artisan migrate
```

6. Ejecutar los seeders (ROLES)

```
php artisan db:seed --class="Database\Seeders\RoleSeeder"
```

7. Correr el servidor de desarrollo

```
php artisan serve
```

8. Empezar a probar la API y explorar routes/api.php
