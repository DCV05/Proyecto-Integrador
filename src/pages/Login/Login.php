<?php

class LoginController
{
  public function index(): void
  {
    return;
  }

  /**
   * Procesa una solicitud de login vía AJAX validando las credenciales.
   *
   * @param array $fields Array con los datos de login
   *
   * @return array Array que contiene:
   *  - 'result'   (int)   : 1 si el login fue exitoso, 0 si falló.
   *  - 'message'  (string): Mensaje de error o vacío si no hubo errores.
   *  - 'redirect' (string): URL de redirección después de un login exitoso.
   */
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
        from ' . DB_PROJECT . '.users
        where
          user_email = "' . $db->pl_esc( $fields['email'] ) . '" and
          enabled = 1
      ';
      $db->pl_query( $sql );
      if( $db->next_row() )
        $row = $db->get_row();
      else
        break;

      // Verificamos las contraseñas
      if( $row['user_password'] )
        $correct = password_verify( $db->pl_esc( $fields['password'] ), $row['user_password'] );

      // Si no existe el usuario o está desactivado, mostramos un error
      if( !$correct )
      {
        $message = pl_label( 'incorrect_user_or_password' );
        break;
      }
      
      // Guardamos el usuario en la sesión
      unset( $row['user_password'] );
      $_SESSION['app']['user'] = $row;

      // Redirigimos al usuario
      $redirect = '/debug';

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