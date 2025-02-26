<?php

class ContactController
{
  public function index(): void
  {
    return;
  }

  // ------------------------------------------------------------------------------------------------------------------
  // AJAX
  // ------------------------------------------------------------------------------------------------------------------

  /**
   * Manda un email a soporte
   * 
   * @param array $fields Datos del email a enviar
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_send_email( array $fields ): array
  {
    $value        = [];
    
    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // --------------------------------------------------------------------------------------------------------------
      // Verificación de campos
      // --------------------------------------------------------------------------------------------------------------

      // Verificamos que el POST contiene todos los campos requeridos
      $required_fields = ['email', 'email_subject', 'message'];

      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          $alert = '
            <div id="alert-email" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-50 flex items-center max-w-xs p-4 text-white bg-red-600 rounded-lg shadow-lg" role="alert">
              ' . app_get_svg_icon( 'exclamation' ) . '
              <div class="ml-3 text-sm font-normal">' . pl_label( 'required_field' ) . ': ' . $required_field . '</div>
            </div>
          ';

          // Rellenamos los objetos a actualizar
          $kwargs   = ['elem' => '#alert-email'];
          $elements = [
              ['selector' => 'body'        , 'method_name' => 'append' , 'value' => $alert]
            , ['selector' => '#alert-email', 'method_name' => 'execute', 'func_name'  => 'show_alert', 'kwargs' => $kwargs]
          ];
          
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // SEND_EMAIL
      // --------------------------------------------------------------------------------------------------------------

      // Definimos el contenido del email
      $title      = $fields['email_subject'];
      $email_html = file_get_contents( ASSETS_PATH . '/contact_email_template.html' );
      $email_html = str_replace(
          ['{{ title }}', '{{ email }}', '{{ message }}']
        , [$title, $fields['email'], $fields['message']]
        , $email_html
      );

      // $user['user_email']
      // Enviamos el email
      $sent = pl_send_email( 'daniel.correa@kodalogic.com', $title, $email_html );
      if( $sent )
      {
        $alert = '
          <div id="alert-email" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-50 flex items-center max-w-xs px-3 py-2 text-white bg-green-600 rounded-lg shadow-lg" role="alert">
            ' . app_get_svg_icon( 'paper-icon' ) . '
            <div class="ml-3 text-sm font-normal">' . pl_label( 'email_sent' ) . '</div>
          </div>
        ';
      }
      else
      {
        $alert = '
          <div id="alert-email" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-50 flex items-center max-w-xs p-4 text-white bg-red-600 rounded-lg shadow-lg" role="alert">
            ' . app_get_svg_icon( 'exclamation' ) . '
            <div class="ml-3 text-sm font-normal">Error</div>
          </div>
        ';
      }

      // --------------------------------------------------------------------------------------------------------------
      // ELEMENTS
      // --------------------------------------------------------------------------------------------------------------

      // Rellenamos los objetos a actualizar
      $kwargs   = ['elem' => '#alert-email'];
      $elements = [
          ['selector' => 'body'        , 'method_name' => 'append' , 'value' => $alert]
        , ['selector' => '#alert-email', 'method_name' => 'execute', 'func_name'  => 'show_alert', 'kwargs' => $kwargs]
      ];

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'   => $result
      , 'message'  => $message
      , 'redirect' => $redirect
      , 'elements' => $elements
    ];

    return $value;
  }
}

?>