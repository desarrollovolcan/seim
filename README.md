# GoCreative Ges

Sistema minimalista para gestión de clientes, proyectos, servicios recurrentes, cobranzas y automatización de correos.

## Requisitos
- PHP 8+
- MySQL 5.7/8.0
- Servidor web (Apache/Nginx)

## Instalación
1. Clona el repositorio.
2. Crea la base de datos e importa `bd/database.sql` (o `bd/database_full.sql` si quieres todas las actualizaciones aplicadas).
3. Configura las credenciales en `app/config/config.php`.
4. Apunta tu servidor web a la raíz del proyecto.
5. Ingresa con:
   - **Usuario:** eisla@gocreative.cl
   - **Contraseña:** Ei1245.$

## Configuración SMTP
- Ingresar en **Configuración -> SMTP** y completar ambas cuentas (Cobranza e Información).
- Las contraseñas se almacenan en la tabla `settings` como JSON.

## Cron Jobs
Ejecuta los jobs desde CLI:

```bash
php cron/run.php check_expirations
php cron/run.php generate_invoices
php cron/run.php send_scheduled_emails
```

Se recomienda configurar cron en el sistema operativo para ejecutarlos según necesidad.

## Flujo recomendado
1. Crear cliente.
2. Crear servicio recurrente.
3. Generar factura (manual o automática).
4. Encolar correo desde Cola de correos.
5. Ejecutar `send_scheduled_emails`.
6. Registrar pago.

## Seguridad
- CSRF en formularios.
- Passwords con `password_hash`.
- Prepared statements en todas las consultas.

## Logs
- `storage/logs/app.log`
