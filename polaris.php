<?php

//------------------------------------------------------------------------------------------------------------------------------------
// POLARIS | PHP FRAMEWORK
//------------------------------------------------------------------------------------------------------------------------------------

ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

// Constantes de rutas
define( 'BASE_PATH'  , __DIR__ );
define( 'MAIN_PATH'  , __DIR__ . '/src' );
define( 'APP_PATH'   , __DIR__ . '/src/app' );
define( 'ASSETS_PATH', __DIR__ . '/src/assets' );

// Inicializamos la sesión y composer
require_once( __DIR__ . '/vendor/autoload.php' );
require_once( APP_PATH . '/sdk.php' );
pl_start();

// Incluímos todas las librerías
require_once( APP_PATH . '/Model.php'      );
require_once( APP_PATH . '/Router.php'     );
require_once( APP_PATH . '/ViewEngine.php' );
require_once( APP_PATH . '/app.php'        );
require_once( APP_PATH . '/Logger.php'     );

// Constantes de DB
// La constante DB_SYS, correspondiente al dominio, se genera al procesar la URL
$config = parse_ini_file( __DIR__ . '/config.ini', true  );
define( 'DB_SERVER'   , $config['mysql']['db_server']   );
define( 'DB_USER'     , $config['mysql']['db_user']     );
define( 'DB_PASSWORD' , $config['mysql']['db_password'] );
define( 'DB_SYS'      , $config['mysql']['db_sys']      );
define( 'DB_PROJECT'  , $config['mysql']['db_project']  );

// -------------------------------------------------------------------------------------
// Monolog Logger
// -------------------------------------------------------------------------------------

// Creamos una variable global Logger para todo el sistema
global $logger;
$logger = ( new AppLogger() )->getLogger();

// -------------------------------------------------------------------------------------
// Table Creator
// -------------------------------------------------------------------------------------

use erguncaner\Table\Table;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;
use erguncaner\Table\TableCell;

// -------------------–-------------------–-------------------–-------------------–
// Constantes de idioma
// -------------------–-------------------–-------------------–-------------------–

// Si está definido en la sesión, usamos ese idioma
if( !defined( 'DEF_LANG' ) && !empty( $_SESSION['polaris']['def_lang'] ) )
  define( 'DEF_LANG', $_SESSION['polaris']['def_lang'] );

// Si no está definido en la sesión, utilizamos el del navegador
elseif( !defined( 'DEF_LANG' ) && empty( $_SESSION['polaris']['def_lang'] ) )
  define( 'DEF_LANG', pl_get_browser_language( ['es', 'en'], 'es' ) );

// -------------------–-------------------–-------------------–-------------------–
// Labels
// -------------------–-------------------–-------------------–-------------------–

// Labels de la aplicación
$labels_json        = file_get_contents( APP_PATH . '/labels.json' );
$_SESSION['labels'] = json_decode( $labels_json, true );

?>