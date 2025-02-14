<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class AdminFinancesController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();

    return;
  }

  /**
   * Genera la tabla de pagos disponibles.
   * 
   * @return string HTML de la tabla de pagos.
   */
  public function table_finances(): string
  {
    $value            = '';
    $mod_users        = new Users();
    $mod_payments     = new Payments();
    $mod_user_details = new UserDetails();

    // Capturamos todos los pagos relacionados con el usuario actual
    $payments = $mod_payments->GetRows();

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'payments_table', 'class' => 'table-ui'] );
    $table->addColumn( 'payment_id2' , new TableColumn( pl_label( 'payment_id2' )   ) );
    $table->addColumn( 'user_name'   , new TableColumn( pl_label( 'user_name' )     ) );
    $table->addColumn( 'amount'      , new TableColumn( pl_label( 'amount' )        ) );
    $table->addColumn( 'status'      , new TableColumn( pl_label( 'status' )        ) );
    $table->addColumn( 'payment_date', new TableColumn( pl_label( 'payment_date' )  ) );
    $table->addColumn( 'email_icon'  , new TableColumn( '' ) );

    // Iteramos cada pago y lo añadimos a la tabla
    foreach( $payments as $payment )
    {
      // Capturamos los datos del usuario vinculado
      $user_details = $mod_user_details->GetRows( $payment['user_id'] )[0];
      $user         = $mod_users->GetRow( $user_details['user_id'] )[0];

      // Generamos el select de estado del pago
      if( $payment['status'] !== 'Paid' )
      {
        $status_html = '
          <select id="payment-status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5">
            <option value="Pending" selected>Pending</option>
            <option value="Paid">Paid</option>
          </select>
        ';

        // Botón para enviar un email al usuario
        $email_icon = '
          <div data-uid2="' . $user['user_id2'] . '" data-pid2="' . $payment['payment_id2'] . '" class="email-icon cursor-pointer p-2 rounded-lg bg-orange-600 flex items-center justify-center">
            ' . app_get_svg_icon( 'email' ) . '
          </div>
        ';
      }
      else
      {
        $status_html  = '<span class="bg-blue-100 text-blue-800 text-sm font-medium px-3.5 py-1 rounded-lg">' . ucfirst( $payment['status'] ) . '</span>';
        $email_icon   = '';
      }

      // Calculamos la diferencia de días entre la fecha del pago y la actual
      $payment_date     = new DateTime( $payment['payment_date'] );
      $diff_date        = ( new DateTime() )->diff( $payment_date );
      $difference_days  = intval( $diff_date->format( '%r%a' ) ); // Incluye signo positivo o negativo

      // Determinamos el color del badge
      if( $difference_days > 30 )
        $badge_color = 'green'; // Más de 30 días antes de la fecha
      elseif ( $difference_days >= 0 )
        $badge_color = 'orange'; // Entre 0 y 30 días antes
      else
        $badge_color = 'red'; // Más de 30 días después

      $date = '
        <span class="bg-' . $badge_color . '-100 text-' . $badge_color . '-800 text-sm font-medium px-3.5 py-1 rounded-lg">
          ' . date( 'd-m-Y', strtotime( $payment['payment_date'] ) ) . '
        </span>
      ';

      // Definimos las celdas
      $cells = [
          'payment_id2'   => new TableCell( $payment['payment_id2'] )
        , 'user_name'     => new TableCell( '<a class="text-blue-500 hover:underline" href="/users?uid2=' . $user_details['detail_id2'] .'">' . $user_details['user_name'] . '</a>' )
        , 'amount'        => new TableCell( number_format( $payment['amount'], 2 ) . ' €' )
        , 'status'        => new TableCell( $status_html )
        , 'payment_date'  => new TableCell( $date )
        , 'email_icon'    => new TableCell( $email_icon, ['class' => 'text-center w-12 icon-container'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $payment['payment_id2']
        , 'class'     => 'hover:bg-gray-100 table-row-link h-6'
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
    return $value;
  }

  /**
   * Genera una fila de la tabla de pagos disponibles.
   * 
   * @return string HTML de la fila de la tabla de pagos.
   */
  public function table_row_finances( string $payment_id2 ): string
  {
    $value            = '';
    $mod_users        = new Users();
    $mod_payments     = new Payments();
    $mod_user_details = new UserDetails();

    // Capturamos todos los pagos relacionados con el usuario actual
    $payment = $mod_payments->GetRow( $payment_id2 )[0];

    // Capturamos los datos del usuario vinculado
    $user_details = $mod_user_details->GetRows( $payment['user_id'] )[0];
    $user         = $mod_users->GetRow( $user_details['user_id'] )[0];

    // Generamos el select de estado del pago
    if( $payment['status'] !== 'Paid' )
    {
      $status_html = '
        <select id="payment-status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-fit p-2.5">
          <option value="Pending" selected>Pending</option>
          <option value="Paid">Paid</option>
        </select>
      ';

      // Botón para enviar un email al usuario
      $email_icon = '
        <div data-uid2="' . $user['user_id2'] . '" data-pid2="' . $payment['payment_id2'] . '" class="email-icon cursor-pointer p-2 rounded-lg bg-orange-600 flex items-center justify-center">
          ' . app_get_svg_icon( 'email' ) . '
        </div>
      ';
    }
    else
    {
      $status_html  = '<span class="bg-blue-100 text-blue-800 text-sm font-medium px-3.5 py-1 rounded-lg">' . ucfirst( $payment['status'] ) . '</span>';
      $email_icon   = '';
    }

    // Calculamos la diferencia de días entre la fecha del pago y la actual
    $payment_date     = new DateTime( $payment['payment_date'] );
    $diff_date        = ( new DateTime() )->diff( $payment_date );
    $difference_days  = intval( $diff_date->format( '%r%a' ) ); // Incluye signo positivo o negativo

    // Determinamos el color del badge
    if( $difference_days > 30 )
      $badge_color = 'green'; // Más de 30 días antes de la fecha
    elseif ( $difference_days >= 0 )
      $badge_color = 'orange'; // Entre 0 y 30 días antes
    else
      $badge_color = 'red'; // Más de 30 días después

    $date = '
      <span class="bg-' . $badge_color . '-100 text-' . $badge_color . '-800 text-sm font-medium px-3.5 py-1 rounded-lg">
        ' . date( 'd-m-Y', strtotime( $payment['payment_date'] ) ) . '
      </span>
    ';

    // Definimos las celdas
    $cells = [
        'payment_id2'   => new TableCell( $payment['payment_id2'] )
      , 'user_name'     => new TableCell( '<a class="text-blue-500 hover:underline" href="/users?uid2=' . $user_details['detail_id2'] .'">' . $user_details['user_name'] . '</a>' )
      , 'amount'        => new TableCell( number_format( $payment['amount'], 2 ) . ' €' )
      , 'status'        => new TableCell( $status_html )
      , 'payment_date'  => new TableCell( $date )
      , 'email_icon'    => new TableCell( $email_icon, ['class' => 'text-center w-12 icon-container'] )
    ];

    // Añadimos la fila
    $table_row =  new TableRow( $cells, [
        'id'        => 'row-' . $payment['payment_id2']
      , 'class'     => 'hover:bg-gray-100 table-row-link h-6'
    ] );

    // Convertimos la tabla a HTML
    $value = $table_row->html();
    return $value;
  }

  // ------------------------------------------------------------------------------------------------------------------
  // AJAX
  // ------------------------------------------------------------------------------------------------------------------

  /**
   * Manda un email al usuario del pago vinculado
   * 
   * @param array $fields Datos del participante a actualizar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_send_email( array $fields ): array
  {
    $value        = [];
    $mod_payments = new Payments();

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
      $required_fields = ['pid2', 'uid2'];

      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          $message = pl_label( 'required_field' ) . ': ' . $required_field;
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // SEND_EMAIL
      // --------------------------------------------------------------------------------------------------------------

      // Capturamos los datos del usuario y del pago
      $payment  = $mod_payments->GetRow( $fields['pid2'] )[0];

      // Formateamos los datos para el email
      $amount = number_format( $payment['amount'], 2 ) . ' €';

      // Definimos el contenido del email
      $title      = 'Recordatorio de pago para el campamento infantil';
      $email_html = file_get_contents( ASSETS_PATH . '/email_template.html' );
      $email_html = str_replace(
          ['{{ amount }}', '{{ date }}']
        , [$amount, $payment['payment_date']]
        , $email_html
      );

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
          <div id="alert-email" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-50 flex items-center max-w-xs p-4 text-white bg-green-600 rounded-lg shadow-lg" role="alert">
            ' . app_get_svg_icon( 'exclamation' ) . '
            <div class="ml-3 text-sm font-normal">Error</div>
          </div>
        ';
      }

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

  /**
   * Actualizar el estado del pago
   * 
   * @param array $fields Datos del participante a actualizar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_change_payment_status( array $fields ): array
  {
    $value  = [];
    $db     = new Model();

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
      $required_fields = ['pid2', 'option'];

      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          $message = pl_label( 'required_field' ) . ': ' . $required_field;
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // SEND_EMAIL
      // --------------------------------------------------------------------------------------------------------------

      // Actualizamos el registro
      $sql = '
        update ' . DB_PROJECT . '.payments
          set status = ?
        where
          payment_id2 = ?
      ';
      $params = [$fields['option'], $fields['pid2']];
      $db->pl_query_prepared( $sql, $params );

      // Recargamos la fila
      $html = $this->table_row_finances( $fields['pid2'] );

      // Rellenamos los objetos a actualizar
      $kwargs     = ['elem' => '#row-' . $fields['pid2'], 'color' => 'green'];
      $elements   = [
          ['selector' => '#row-' . $fields['pid2'], 'method_name' => 'update' , 'value'      => $html]
        , ['selector' => '#row-' . $fields['pid2'], 'method_name' => 'execute', 'func_name'  => 'highlight_row', 'kwargs' => $kwargs]
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