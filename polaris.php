<?php

//------------------------------------------------------------------------------------------------------------------------------------
// POLARIS | PHP FRAMEWORK
//------------------------------------------------------------------------------------------------------------------------------------

ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

// Constantes de rutas
define( 'APP_PATH', __DIR__ . '/app' );
define( 'BASE_PATH', __DIR__ );

// Incluímos todas las librerías
require_once( APP_PATH . '/model.php'       );
require_once( APP_PATH . '/router.php'      );
require_once( APP_PATH . '/view_engine.php' );
require_once( APP_PATH . '/sdk.php'         );
require_once( APP_PATH . '/app.php'         );

// Constantes de DB
// La constante DB_NAME, correspondiente al dominio, se genera al procesar la URL
$config = parse_ini_file( __DIR__ . '/config.ini', true  );
define( 'DB_SERVER'   , $config['mysql']['db_server']   );
define( 'DB_USER'     , $config['mysql']['db_user']     );
define( 'DB_PASSWORD' , $config['mysql']['db_password'] );
define( 'DB_NAME'     , $config['mysql']['db_name']     );

// Inicializamos la sesión
session_start();

/*
  Array | $_SERVER
    [POLARIS_SERVER] => ********
    [POLARIS_USER] => ********
    [POLARIS_PASSWORD] => *******
    [POLARIS_DB] => ******
    [HTTP_HOST] => ********
    [HTTP_USER_AGENT] => Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:123.0) Gecko/20100101 Firefox/123.0
    [HTTP_ACCEPT] => text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*\/*;q=0.8
    [HTTP_ACCEPT_LANGUAGE] => es-ES,es;q=0.8,en-US;q=0.5,en;q=0.3
    [HTTP_ACCEPT_ENCODING] => gzip, deflate
    [HTTP_CONNECTION] => keep-alive
    [HTTP_UPGRADE_INSECURE_REQUESTS] => 1
    [HTTP_PRAGMA] => no-cache
    [HTTP_CACHE_CONTROL] => no-cache
    [PATH] => /usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
    [SERVER_SIGNATURE] => <address>Apache/2.4.57 (Debian) Server at ******** Port 80</address>

    [SERVER_SOFTWARE] => Apache/2.4.57 (Debian)
    [SERVER_NAME] => ********
    [SERVER_ADDR] => ********
    [SERVER_PORT] => 80
    [REMOTE_ADDR] => ********
    [DOCUMENT_ROOT] => /var/www/html
    [REQUEST_SCHEME] => http
    [CONTEXT_PREFIX] => 
    [CONTEXT_DOCUMENT_ROOT] => /var/www/html
    [SERVER_ADMIN] => ********
    [SCRIPT_FILENAME] => /var/www/html/polaris/debug.php
    [REMOTE_PORT] => *****
    [GATEWAY_INTERFACE] => CGI/1.1
    [SERVER_PROTOCOL] => HTTP/1.1
    [REQUEST_METHOD] => GET
    [QUERY_STRING] => 
    [REQUEST_URI] => /polaris/debug.php
    [SCRIPT_NAME] => /polaris/debug.php
    [PHP_SELF] => /polaris/debug.php
    [REQUEST_TIME_FLOAT] => 1710448972.0606
    [REQUEST_TIME] => 1710448972
*/

// Escapamos los caracteres especiales de la URI
$url_relative = filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL );
$url_base     = str_replace( '?' . $_SERVER['QUERY_STRING'], '', $url_relative );

// Capturamos los valores de la página
$polaris = [
    'domain' 		    => $_SERVER['HTTP_HOST']
  ,	'url_abs' 		  => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $url_relative
  ,	'url_relative' 	=> $url_relative
  ,	'url_base' 	    => $url_base
  ,	'url_get' 		  => $_GET
  ,	'document_root'	=> $_SERVER['DOCUMENT_ROOT']
];

$_SESSION['polaris'] = $polaris;

?>