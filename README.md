# Instalación

- Ubicación del [front](https://github.com/fapb92/admin-users-front)

copiar y pegar .env.example en un nuevo archivo .env

-   crear una base de datos para el programa

-   modificar archivo php.init y descomentar la linea "extension=gd"

ejecutar:

```sh
composer install
```

```sh
php artisan optimize:clear
php artisan migrate
php artisan passport:install
```

copiar y pegar los ids entregados por passport:install en las variables PASSPORT del .env
