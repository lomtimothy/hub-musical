# Hub Musical

Hub Musical es una aplicación web desarrollada con Laravel para reservar estudios musicales, gestionar sesiones de grabación, subir demos, generar split sheets, enviar correos y procesar pagos.

## Tecnologías

- Laravel 13
- PHP 8.5
- MySQL
- Laravel Herd
- Livewire starter kit
- Flux UI
- Tailwind CSS
- Eloquent ORM
- Laravel Policies
- Laravel Queues
- Laravel Task Scheduling
- Laravel Socialite
- Stripe Checkout
- DomPDF

## Funcionalidades principales

- Registro, login y logout.
- Verificación de correo.
- Login con GitHub.
- Roles de usuario: músico, productor y admin.
- CRUD de estudios.
- Reserva de sesiones.
- Validación de formularios en servidor y cliente.
- Carga, listado, descarga y eliminación de archivos de audio.
- Relación muchos a muchos entre sesiones y músicos con instrumento y porcentaje de split.
- Tags polimórficos para estudios y usuarios.
- PDF Split Sheet.
- Correo personalizado al reservar sesión.
- Jobs y queues para envío de correos.
- Recordatorios automáticos con Task Scheduling.
- Pagos con Stripe Checkout.
- Pruebas automatizadas con Pest.

## Instalación local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
