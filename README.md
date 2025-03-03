# Proyecto Integrador - Campamento Infantil

## Descripción

Este proyecto es una aplicación web desarrollada como parte del Proyecto Integrador del ciclo formativo de Desarrollo de Aplicaciones Web. Su propósito es facilitar la gestión de un campamento infantil mediante una plataforma digital que permite la inscripción de participantes, la administración de actividades y la supervisión de monitores.

La aplicación ha sido desarrollada utilizando una arquitectura **Modelo-Vista-Controlador (MVC)** y tecnologías modernas para garantizar seguridad, escalabilidad y eficiencia en la gestión de datos.

## Tecnologías utilizadas

### Backend
- **PHP**: Lenguaje principal del lado del servidor.
- **Polaris**: Framework utilizado para la gestión de rutas y controladores.
- **MySQL**: Base de datos relacional para almacenamiento y gestión de la información.
- **Composer**: Gestor de dependencias de PHP.
- **Monolog**: Sistema de logging para el registro de eventos en el servidor.
- **PHPUnit**: Framework para la realización de pruebas unitarias.

### Frontend
- **HTML5**: Estructura de la aplicación web.
- **CSS3 / TailwindCSS**: Estilización de la interfaz y diseño responsive.
- **JavaScript / jQuery**: Interactividad y gestión dinámica de la UI.
- **AJAX**: Comunicación asíncrona entre frontend y backend.

### Despliegue y configuración
- **Apache**: Servidor web configurado con `.htaccess`.
- **Docker**: Contenedores para gestionar entornos de desarrollo y producción.
- **Git/GitHub**: Control de versiones y almacenamiento del código fuente.
- **Debian 12**: Sistema operativo utilizado en entornos de desarrollo y producción.

## Estructura del Proyecto

```
📂 src/
 ├── kernel.php         # Centro de control de solicitudes de Polaris
 ├── polaris.php        # Inicialización de Polaris
 ├── .htaccess          # Configuración de Apache para rutas
 ├── composer.json      # Dependencias de PHP
 ├── config.ini         # Configuración de la base de datos
 ├── 📂 apache/         # Configuraciones específicas de Apache
 ├── 📂 app/            # Módulos de Polaris
 │   ├── Router.php     # Enrutador
 │   ├── Model.php      # Clase de base de datos
 │   ├── ViewEngine.php # Compilador de templates
 │   ├── sdk.php        # Funciones generales (depuración, redirección...)
 │   ├── app.php        # Funciones globales de los templates
 │   ├── labels.json    # Labels de la aplicación
 │   └── Logger.php     # Socket global de Monolog
 │
 ├── 📂 assets/         # Archivos estáticos (imágenes, iconos, fuentes)
 ├── 📂 css/            # Estilos y personalización visual
 │ 
 ├── 📂 init/           # Bases de datos
 │   ├── 📂 polaris/    # Configuración de Polaris
 │   │   ├── init.php   # Inicialización del framework
 │   │   └── init.sql   # Scripts SQL de inicialización
 │   └── 📂 project/    # Configuración del proyecto
 │       ├── init.php   # Variables y constantes globales
 │       └── init.sql   # Base de datos inicial
 │ 
 ├── 📂 js/             # Scripts de interacción cliente-servidor
 ├── 📂 models/         # Modelos de la base de datos
 ├── 📂 pages/          # Vistas y controladores para cada sección
 │   ├── Activities/
 │   │   ├── Activities.html
 │   │   ├── Activities.js
 │   │   └── Activities.php
 │   ├── Login/
 │   │   ├── Login.html
 │   │   ├── Login.js
 │   │   └── Login.php
 │   └── Tutor/
 │       └── Account/
 │           ├── Account.html
 │           ├── Account.js
 │           └── Account.php
 │ 
 ├── 📂 tests/          # PHPUnit
 ├── 📂 vendor/         # Dependencias de Composer
 ├── .gitignore         # Archivos y carpetas ignorados en Git
 └── README.md          # Documentación del proyecto
```

## Instalación y Configuración

### 1. Clonar el repositorio
```bash
git clone git@github.com:DCV05/Proyecto-Integrador.git
cd Proyecto-Integrador
composer install
```

### 2. Configuración del Servidor Apache
Es necesario modificar la configuración de Apache para permitir el uso de `.htaccess`. Edita el archivo de configuración de Apache (`apache2.conf` en Debian/Ubuntu) y cambia:

```apache
<Directory /var/www/html/proyecto_integrador>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Luego, reinicia Apache:
```bash
sudo systemctl restart apache2
```

### 3. Configuración de la Base de Datos
El archivo `config.ini` debe configurarse correctamente para que la aplicación pueda conectarse a la base de datos MySQL:

```ini
[mysql]
db_server   = localhost
db_user     = root
db_password = root
db_sys      = polaris
db_project  = proyecto_integrador
```


### 4. Configuración de AJAX
El archivo `crypt_config.ini` debe configurarse con una llave privada de encriptación para poder realizar consultas AJAX dentro del sistema:

```ini
[pl_encrypt]
private_key = ad05f78187c942f9dd521605fa81f1ba
```

## Flujos del sistema Polaris
Dentro del entorno de Polaris, existen diferentes flujos y procesos que pueden facilitar el desarrollo, seguridad y escalabilidad de un proyecto. A continuación, se presentan unos diagramas con los diferentes flujos de Polaris:

### **Flujo de Enrutamiento y Carga del Controlador**

```mermaid
graph TD;
    A[Solicitud HTTP] -->|Verifica si es AJAX| B{Es una solicitud AJAX?}
    B -- Sí --> C[Cargar Router y obtener ruta AJAX]
    B -- No --> D[Cargar Router y obtener ruta normal]

    C --> E{Ruta encontrada en la BD?}
    E -- No --> F[Retornar error 404]
    E -- Sí --> G[Ejecutar controlador correspondiente]
    
    D --> H{Existe el archivo de la ruta?}
    H -- No --> I[Redirigir a 404]
    H -- Sí --> J[Cargar controlador]

    J --> K[Ejecutar controlador y método]
    K --> L[Renderizar vista con ViewEngine]
    L --> M[Respuesta al cliente]
```


### **Flujo de Procesamiento de AJAX**

```mermaid
graph TD;
    A[Solicitud AJAX] -->|Captura parámetros| B[Determina el controlador y método]
    B --> C{Método existe?}
    C -- No --> D[Retornar error JSON]
    C -- Sí --> E[Llamar a la función AJAX del controlador]
    E --> F{La función devuelve datos?}
    F -- No --> G[Retornar error JSON]
    F -- Sí --> H[Formatear respuesta en JSON]
    H --> I[Enviar respuesta al cliente]
```


### **Flujo de Renderizado con ViewEngine**

```mermaid
graph TD;
    A[ViewEngine recibe una plantilla] -->|Carga contenido| B[Parsea etiquetas Polaris]
    B --> C[Busca variables en la sesión y controlador]
    C --> D{Etiqueta encontrada?}
    
    D -- No --> E[Reemplazar por &quot;variable no encontrada&quot;] --> F[Sustituir en plantilla]
    D -- Sí --> F[Sustituir en plantilla]
    
    F --> G[Compilar plantilla]
    G --> H[Insertar controlador en sesión]
    H --> I[Renderizar HTML final]
    I --> J[Enviar contenido al cliente]
```

### **Flujo de Serialización de Controladores en la Sesión**

```mermaid
graph TD;
    A[Ejecutar un controlador] -->|Verifica existencia en sesión| B{¿Existe en $_SESSION?}
    
    B -- Sí --> C[Recuperar controlador desde $_SESSION]
    B -- No --> D[Crear nueva instancia del controlador]
    
    D --> E[Generar un hash único para la instancia]
    E --> F[Almacenar en el $_SESSION]
    
    C --> G[Ejecutar método del controlador]
    F --> G[Ejecutar método del controlador]
    
    G --> H[Retornar respuesta]
```

## Contribuciones y Contacto
Para reportar errores o contribuir con mejoras, puedes abrir un issue en el repositorio de GitHub o contactar a **daniel.correa@kodalogic.com**.
