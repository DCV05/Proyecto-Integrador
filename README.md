# Proyecto Integrador

## Rutas

- **Login:** /login
- **Signup:** /signup
- **TutorEdit:** /tutor/edit
- **ParticipantEdit:** /participant/edit

## Requisitos

Este proyecto requiere configuraciones adicionales en el servidor Apache para funcionar correctamente. Es importante habilitar el uso de archivos `.htaccess` para que las configuraciones específicas del proyecto sean reconocidas.

1. **Servidor Apache:** Asegúrate de que Apache está instalado y funcionando correctamente.
2. **Permisos para `.htaccess`:** Es necesario que Apache permita la ejecución de archivos `.htaccess` en el directorio correspondiente.

## Instalación

Para instalar el proyecto, sigue estos pasos:

1. Clona el repositorio desde GitHub:

   ```bash
   git clone git@github.com:DCV05/Proyecto-Integrador.git
   ```

2. Navega al directorio del proyecto:

   ```bash
   cd Proyecto-Integrador
   ```

3. Configura Apache según las instrucciones a continuación.

## Configuración de Apache

Para habilitar el uso de archivos `.htaccess`, sigue estos pasos:

1. Abre el archivo de configuración principal de Apache. Normalmente se encuentra en:

   - **Debian/Ubuntu:** `/etc/apache2/apache2.conf`

2. Busca el bloque `<Directory>` que corresponde al directorio de tu proyecto. Por ejemplo:

   ```apache
   <Directory /var/www/html/proyecto_integrador>
       Options Indexes FollowSymLinks
       AllowOverride None
       Require all granted
   </Directory>
   ```

3. Modifica el valor de `AllowOverride` de `None` a `All` para permitir que los archivos `.htaccess` sean ejecutados. Debería quedar así:

   ```apache
   <Directory /var/www/html/proyecto_integrador>
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

4. Guarda los cambios y reinicia el servicio de Apache para que la configuración tome efecto. Puedes usar el siguiente comando:

   ```bash
   sudo systemctl restart apache2
   ```

## Notas importantes

- Si no realizas esta configuración, el proyecto podría no funcionar correctamente debido a la dependencia de configuraciones en los archivos `.htaccess`.
- Asegúrate de que el archivo `.htaccess` esté presente en el directorio raíz de tu proyecto.

## Configuración de MySQL

Para que las conexiones de MySQL funcionen correctamente, será necesario modificar el archivo `config.ini`, ya que es este archivo el que contiene las claves de acceso al servicio de base de datos.

Ejemplo de `config.ini`:

```ini
[mysql]
db_server   = mariadb
db_user     = root
db_password = root
db_sys      = polaris
db_project  = proyecto_integrador
```

En el caso de que se quiera conectar a MySQL mediante XAMMP, será necesario cambiar las claves a las siguientes:

```ini
[mysql]
db_server   = localhost
db_user     = root
db_password = root
db_sys      = polaris
db_project  = proyecto_integrador
```

## Soporte

- En el caso de que surja algún problema en su instalación o ejecución, contacte al correo daniel.correa@kodalogic.com para poder solucionarlo.
