<?php

//-------------------------------------------------------------------------------
// Funciones de depuración
//-------------------------------------------------------------------------------

/**
 * Función para imprimir un array de forma formateada
 * @param array $arr
 * @param boolean $return
 */
function pl_dump( array $arr, bool $return = false )
{
  // Si definimos que no debe haber un return, mostramos los datos
  if( !$return )
  {
    // Generamos la cabecera de respuesta
    header( "Expires: Sun, 19 Nov 1978 05:00:00 GMT"               );
    header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"  );
    header( "Cache-Control: no-store, no-cache, must-revalidate"   );
    header( "Cache-Control: post-check=0, pre-check=0", false      );
    header( "Pragma: no-cache"                                     );
    header( "Content-type: application/json; charset: utf-8", true );	  
    header( "HTTP/1.0 400"                                         );
  }

  // El Backtrace proporciona información de las funciones usadas en un script php
  $backtrace = debug_backtrace();

  // El array shift es usado para eliminar el primer elemento de un array origen y almacenarlo
  $caller = array_shift( $backtrace );

  // Capturamos el archivo, la línea y la función origen
  $file     = $caller['file'];
  $line     = $caller['line'];
  $function = $caller['function'];

  // Inicializamos las variables de formato
  $chr  = '';
  $i    = 0;

  // Líneas de separación
  while( $i < 75 )
  {
    $chr .= html_entity_decode( '&#x2212;', ENT_NOQUOTES, 'UTF-8' );
    $i++;
  }

  // Return
  if( $return )
    return $arr;
  else
  {
    echo "\n{$chr} \n{$file} | {$line} | {$function} \n{$chr} \n\n";
    print_r( $arr );
  }
}

/**
 * Función para capturar parámetros del GET
 * @param string $param_name
 * @param string $default_value
 * @return mixed
 */
function pl_get( $param_name, $default_value = null ): mixed
{
  return $_GET[$param_name] ?? $default_value;
}

/**
 * Función para redirigir
 * @param string $url
 */
function pl_redirect( $url ): void
{
  header( 'Location: ' . $url );
}

/**
 * Función para rellenar con ceros un número
 * @param string $number
 * @return string $value
 */
function pl_number_id( string $number, int $zeros = 4 ): string
{
	$value = sprintf( '%0' . $zeros . 'd', $number );
	return $value;
}

/**
 * Devuelve el idioma del navegador.
 * 
 * @param  array   $available   Lista de idiomas disponibles para el sitio.
 * @param  string  $default     Idioma predeterminado del sitio.
 * @return string               Código del idioma detectado.
 */
function pl_get_browser_language( array $available = [], string $default = 'en' ): string
{
  // Valor por defecto
  $value = $default;

  do
  {
    // Si hay cabecera de lenguaje
    if( !isset( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) )
      break;

    // Dividimos los idiomas disponibles
		$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );

    // Si no hay idiomas disponibles definidos, capturamos el primer idioma detectado
		if( empty( $available ) && !empty( $langs ) )
    {
      $value = substr( $langs[0], 0, 2 );
      break;
    }

    // Verificar cada idioma detectado
		foreach( $langs as $lang )
    {
      // Extraemos el código del idioma
			$lang = substr( $lang, 0, 2 );

      // Verificar si coincide con la lista de idiomas disponibles
			if( in_array( $lang, $available ) )
			{
        $value = $lang;
        break 2;
      }
		}
    
  } while( false );

  return $value;
}

?>