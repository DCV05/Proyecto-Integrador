<?php

// Inicializamos MySQL
$db = new pl_model( 'mysql' );

try
{
  // Comprobamos que la DB existe
  $sql = 'show databases like "%polaris%"';
  $db->pl_query( $sql );
  if( $db->get_num_rows() > 0 )
    throw new Exception();

  // Capturamos el contenido del SQL para inicializar la DB y lo ejecutamos
  $sql = file_get_contents( __DIR__ . '/init.sql' );
  $db->multi_query( $sql );
}
catch( Exception $e ) {}
finally
{
  $db->close();
}
?>