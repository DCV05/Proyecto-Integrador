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
    app_restrict();

    return;
  }

  /**
   * Genera la tabla de pagos disponibles.
   * 
   * @return string HTML de la tabla de pagos.
   */
  public function table_finances( string $where = '' ): string
  {
    $value            = '';
    $mod_users        = new Users();
    $mod_payments     = new Payments();
    $mod_user_details = new UserDetails();

    // Capturamos todos los pagos relacionados con el usuario actual
    $payments = $mod_payments->GetRows( $where );

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'payments_table', 'class' => 'p-table'] );
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
      $user_details = $mod_user_details->GetRowsUser( $payment['user_id'] )[0];
      $user         = $mod_users->GetRow( $user_details['user_id'] )[0];

      // Generamos el select de estado del pago
      if( $payment['status'] !== 1 )
      {
        $status_html = '
          <select id="payment-status" class="p-select">
            <option value="0" selected>Pending</option>
            <option value="1">Paid</option>
          </select>
        ';

        // Botón para enviar un email al usuario
        $email_icon = '
          <div data-uid2="' . $user['user_id2'] . '" data-pid2="' . $payment['payment_id2'] . '" class="email-icon p-button cursor-pointer">
            ' . app_get_svg_icon( 'email' ) . '
          </div>
        ';
      }
      else
      {
        $status_html  = '<span class="p-tag-blue">' . pl_label( 'paid' ) . '</span>';
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
    $user_details = $mod_user_details->GetRowsUser( $payment['user_id'] )[0];
    $user         = $mod_users->GetRow( $user_details['user_id'] )[0];

    // Generamos el select de estado del pago
    if( $payment['status'] !== 1 )
    {
      $status_html = '
        <select id="payment-status" class="p-select">
          <option value="0" selected>Pending</option>
          <option value="1">Paid</option>
        </select>
      ';

      // Botón para enviar un email al usuario
      $email_icon = '
        <div data-uid2="' . $user['user_id2'] . '" data-pid2="' . $payment['payment_id2'] . '" class="email-icon p-button cursor-pointer">
          ' . app_get_svg_icon( 'email' ) . '
        </div>
      ';
    }
    else
    {
      $status_html  = '<span class="p-tag-blue">' . pl_label( 'paid' ) . '</span>';
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

  // --------------------------------------------------------------------------------
  // AJAX
  // --------------------------------------------------------------------------------

  public function ajax_form_search( array $fields ): array
  {
    $value  = [];

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    $db = new Model();

    do
    {
      // Creamos el filtro
      $where = ' where 1 = 1'; // Base para concatenar con AND

      if( !empty( $fields['query'] ) )
        $where .= ' and payment_id2 like "%' . $db->esc( $fields['query'] ) . '%"';
      
      if( !empty( $fields['status'] ) || $fields['status'] !== '' )
        $where .= ' and status = ' . $db->esc( $fields['status'] );

      // Recargamos el HTML filtrado
      $html = $this->table_finances( $where );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#payments_table', 'method_name'  => 'update' , 'value' => $html]
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
   * Manda un email al usuario del pago vinculado
   * 
   * @param array $fields Datos del participante a actualizar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_send_email( array $fields ): array
  {
    $value        = [];
    $mod_payments = new Payments();
    $mod_users    = new Users();

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
      $user     = $mod_users->GetRow( $fields['uid2'] );

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

      // $user['user_email']
      // Enviamos el email
      $sent     = pl_send_email( 'daniel.correa@kodalogic.com', $title, $email_html );
      $message  = $sent === true ? pl_label( 'email_sent' ) : 'Error';
      $elements = app_generate_alert( !$sent, $message );

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
      $db->pl_query_prepared( $sql, $params, false, true );

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