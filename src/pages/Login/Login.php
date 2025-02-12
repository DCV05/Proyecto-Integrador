<?php

class LoginController
{
  public function index(): void
  {
    // Cerramos la sesión del usuario
    if( !empty( $_SESSION['app']['user'] ) )
      unset( $_SESSION['app']['user'] );
    
    // Destruímos los controladores
    if( !empty( $_SESSION['controllers'] ) )
      unset( $_SESSION['controllers'] ); 

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
      // --------------------------------------------------------------------------------------------------------------
      // Verificación de campos
      // --------------------------------------------------------------------------------------------------------------

      // Verificamos que el POST contiene todos los campos requeridos
      $required_fields = ['email', 'password'];
      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          $message = pl_label( 'incorrect_user_or_password' );
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // Login
      // --------------------------------------------------------------------------------------------------------------

      // Buscamos si existe un usuario con las credenciales recibidas
      $sql = '
        select
          *
        from ' . DB_PROJECT . '.users
        where
          user_email = "' . $db->esc( $fields['email'] ) . '" and
          enabled = 1
      ';
      $db->pl_query( $sql );
      if( $db->next_row() )
        $row = $db->get_row();
      else
      {
        $message = pl_label( 'incorrect_user_or_password' );
        break;
      }

      // Verificamos las contraseñas
      if( $row['user_password'] )
        $correct = password_verify( $db->esc( $fields['password'] ), $row['user_password'] );

      // Si no existe el usuario o está desactivado, mostramos un error
      if( !$correct )
      {
        $message = pl_label( 'incorrect_user_or_password' );
        break;
      }
      
      // Calculamos la foto de perfil y el nombre del fichero
      $assets_dir = match( intval( $row['role'] ) )
      {
          0 => ASSETS_PATH . '/panel/tutors'
        , 1 => ASSETS_PATH . '/panel/monitors'
        , 2 => ASSETS_PATH . '/panel/admins'
      };
      $file_name = pl_number_id( $row['user_id'] ) . '_' . $row['user_id2'];
      
      // Buscamos el fichero y capturamos la imagen
      $files = glob( $assets_dir . '/' . $file_name . '.*' );
      $row['user_image'] = !empty( $files )
        ? '<img src="' . str_replace( $_SESSION['polaris']['document_root'], $_SESSION['polaris']['complex_domain'], reset( $files ) ) . '" class="w-10 h-10 mr-2 rounded-full shadow-landing border-2 border-gray-300">'
        : '<div class="flex items-center justify-center w-10 h-10 bg-blue-500 text-white font-bold rounded-full">' . ucfirst( $row['user_email'][0] ) . '</div>';

      // Casteo del rol
      $row['role'] = intval( $row['role'] );

      // Guardamos el usuario en la sesión
      unset( $row['user_password'] );
      $_SESSION['app']['user'] = $row;

      // Dependiendo del tipo de usuario mostramos un formulario u otro
      $redirect = match( intval( $row['role'] ) )
      {
          0       => '/tutor/desktop'
        , 1       => '/monitor/desktop'
        , 2       => '/admin/desktop'
        , default => ''
      };

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'    => $result
      , 'message'   => $message
      , 'redirect'  => $redirect
    ];

    $db->close();

    return $value;
  }
}

?>