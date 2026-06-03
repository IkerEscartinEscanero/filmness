# FILMNESS 🎭
FilmNess es mi proyecto final de DAW (Desarrollo de Aplicaciones Web). En este README dejo solo lo importante para la memoria: instalación en local, despliegue en Render y funcionamiento de las pruebas.

## 1. Instalación y configuración en local

Este es el proceso que se sigue para levantar el proyecto en un entorno local con Docker.

### Requisitos previos

1. Git.
2. PHP 8.4 o superior.
3. Composer.
4. Node.js 22 LTS o superior.
5. NPM.
6. Docker Desktop.

### Pasos de instalación

1. Clonar el repositorio y situarse dentro de la carpeta del proyecto.

```bash
git clone <URL-del-repositorio>
cd filmness
```

2. Instalar las dependencias del proyecto.

```bash
composer install
npm install
```

3. Copiar el fichero de entorno `.env.example` con el nombre `.env` duplicando el archivo o, desde PowerShell:

```powershell
Copy-Item .env.example .env
```

4. Configurar el archivo `.env` con los valores necesarios: nombre de la aplicación, URL, base de datos, correo y Stripe si se van a probar pagos reales.

5. Generar la clave de aplicación de Laravel.

```bash
php artisan key:generate
```

6. Levantar la base de datos con Docker.

```bash
docker compose up -d
```

7. Ejecutar las migraciones.

```bash
php artisan migrate
```

8. Ejecutar los seeders para cargar los datos iniciales.

```bash
php artisan db:seed
```

9. Crear el enlace simbólico para los archivos de almacenamiento.

```bash
php artisan storage:link
```

10. Iniciar la aplicación en local.

```bash
npm run local
```

Cuando todo está correcto, la aplicación queda accesible en `http://localhost:8000`.

### Webhook de Stripe en local

Para probar los pagos en local, es necesario usar la CLI de Stripe y reenviar los eventos al webhook de la aplicación:

```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

El secreto generado por Stripe (`STRIPE_WEBHOOK_SECRET`) debe copiarse en el archivo `.env`.

## 2. Despliegue en Render

FilmNess está preparado para desplegarse en Render mediante un contenedor Docker.

### Pasos generales del despliegue

1. Crear una cuenta en Render.
2. Subir el proyecto a GitHub con todos los cambios.
3. Crear un nuevo Web Service en Render y conectar el repositorio.
4. Elegir `Runtime Docker`.
5. Crear un disco persistente para `/var/www/html/storage`.
6. Configurar las variables de entorno de producción.
7. Desplegar la aplicación.

### Variables importantes en producción

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=...`
- `APP_URL=https://tu-proyecto.onrender.com`
- `DB_CONNECTION=sqlite`
- `DB_DATABASE=/var/www/html/storage/database.sqlite`
- `FILESYSTEM_DISK=public`
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=sync`

Si se van a usar pagos reales y envío de correos, también hay que configurar:

- `STRIPE_KEY`
- `STRIPE_SECRET`
- `STRIPE_WEBHOOK_SECRET`
- `MAIL_MAILER`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`

## 3. Pruebas

El proyecto incluye una base de pruebas automatizadas con Pest y Laravel.

### Cómo funcionan

1. Las pruebas se ejecutan con `php artisan test`.
2. Laravel usa el fichero `.env.testing`, que aísla completamente el entorno de pruebas.
3. Ese entorno utiliza SQLite en memoria, sesiones en `array`, caché en `array` y correo en `array`.
4. Así se evita tocar la base de datos local real o la configuración de producción.

### Qué cubren las pruebas

- Autenticación.
- Registro de usuarios.
- Verificación de correo.
- Confirmación y reseteo de contraseña.
- Actualización del perfil.
- Acceso al panel de administración.
- Ejemplos básicos de comportamiento funcional.

### Workflow de GitHub Actions

También se ha configurado un workflow en GitHub Actions para ejecutar la suite automáticamente en cada `push` y `pull_request`.

### Comando recomendado

```bash
composer run test
```

Este comando limpia la configuración antes de lanzar los tests, para evitar problemas de caché.
