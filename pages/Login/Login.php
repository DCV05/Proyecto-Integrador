<?php

class LoginController
{
  public function index(): void
  {
    return;
  }

  public function ajax_login( array $fields ): array
  {
    $value  = [];
    $db     = new pl_model();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';

    do
    {
      // Buscamos si existe un usuario con las credenciales recibidas
      $sql = '
        select
          *
        from ' . DB_NAME . '.users
        where
          user_email = "' . $db->pl_esc( $fields['email'] ) . '" and
          user_password = "' . $db->pl_esc( $fields['password'] ) . '" and
          enabled = 1
      ';
      $row = $db->pl_query( $sql );

      // Si no existe el usuario o está desactivado, mostramos un error
      if( empty( $row ) )
      {
        // Mensaje de error
        $message = 'Usuario o contraseña incorrectos';
        break;
      }

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'    => $result
      , 'message'   => $message
      , 'redirect'  => $redirect
    ];
    return $value;
  }
}

?>