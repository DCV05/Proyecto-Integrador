<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class MonitorAttendanceController
{
  public string|null $activity_id2;
  public array $activity;
  public function index(): void
  {
    // Control de seguridad
    app_security();

    $mod_activities = new Activities();

    // Capturamos el id2 de la actividad
    $this->activity_id2 = pl_get( 'aid2', null );
    if( !$this->activity_id2 )
      pl_redirect( '/monitor/activities' );

    // Buscamos en la DB la actividad. En caso de que no exista, redirigimos al activities
    $this->activity = $mod_activities->GetRow( $this->activity_id2 );
    if( empty( $this->activity ) )
      pl_redirect( '/monitor/activities' );

    return;
  }

  // --------------------------------------------------------------------------------
  // Detalles de la actividad
  // --------------------------------------------------------------------------------

  /**
   * Datos de la actividad.
   * 
   * @return string HTML con los detalles de la actividad y la tabla de asistencia.
   */
  public function activity_details(): string
  {
    // Datos de la Actividad
    $value = '
      <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">' . $this->activity['activity_name'] . '</h2>
        <p class="text-gray-600 mt-2">' . nl2br( $this->activity['activity_description'] ) . '</p>
        <p class="text-gray-500 text-sm mt-2">' . date('F j, Y - H:i', strtotime( $this->activity['activity_time'] ) ) . '</p>
      </div>
    ';

    return $value;
  }

  /**
   * Genera la tabla de asistencia con inputs para hora de llegada/salida.
   * 
   * @return string HTML de la tabla de asistencia.
   */
  public function table_attendance(): string
  {
    $value          = '';
    $mod_attendance = new Attendance();

    // Obtenemos la lista de asistencia
    $attendances = $mod_attendance->GetAttendanceDetails( $this->activity['activity_id'] );

    // Tabla de Asistencia
    $table = new Table( ['id' => 'attendance_table', 'class' => 'table-ui', 'data-activity' => $this->activity['activity_id2']] );

    // Columnas
    $table->addColumn( 'participant_name' , new TableColumn( pl_label( 'participant_name' ), ['id' => 'p_name_col'] ) );
    $table->addColumn( 'checkin_datetime' , new TableColumn( pl_label( 'checkin' )         , ['id' => 'p_checkin_col'] ) );
    $table->addColumn( 'checkout_datetime', new TableColumn( pl_label( 'checkout' )        , ['id' => 'p_checkout_col'] ) );

    // Iteramos cada registro de asistencia
    foreach( $attendances as $entry )
    {
      $participant = $entry['participant'][0];
      $attendance  = $entry['attendance'];

      // Check-in
      if( empty( $attendance['checkin_datetime'] ) )
      {
        $checkin_input = '
          <button class="btn-checkin bg-green-500 text-white px-3 py-1 rounded-lg" data-pid2="' . $participant['participant_id2'] . '">
            ' . pl_label( 'mark_checkin' ) . '
          </button>
        ';
      }
      else
      {
        $checkin_input = '<input type="datetime-local" class="checkin-input w-full border px-2 py-1 rounded" 
          value="' . date( 'Y-m-d\TH:i', strtotime( $attendance['checkin_datetime'] ) ) . '" 
          data-pid2="' . $participant['participant_id2'] . '">';
      }

      // Check-out
      if( empty( $attendance['checkout_datetime'] ) )
      {
        $checkout_input = '
          <button class="btn-checkout bg-red-500 text-white px-3 py-1 rounded-lg" data-pid2="' . $participant['participant_id2'] . '">
            ' . pl_label( 'mark_checkout' ) . '
          </button>
        ';
      }
      else
      {
        $checkout_input = '<input type="datetime-local" class="checkout-input w-full border px-2 py-1 rounded" 
          value="' . date( 'Y-m-d\TH:i', strtotime( $attendance['checkout_datetime'] ) ) . '" 
          data-pid2="' . $participant['participant_id2'] . '">';
      }

      // Definimos las celdas
      $cells = [
          'participant_name'  => new TableCell( $participant['participant_name'] )
        , 'checkin_datetime'  => new TableCell( $checkin_input )
        , 'checkout_datetime' => new TableCell( $checkout_input )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $participant['participant_id2']
        , 'class'     => 'hover:bg-gray-100 table-row-link cursor-pointer'
        , 'data-href' => '/monitor/participant?pid2=' . $participant['participant_id2']
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
    return $value;
  }

  /**
   * Genera una fila de asistencia con inputs para hora de llegada/salida.
   * 
   * @return string HTML de la tabla de asistencia.
   */
  public function table_row_attendance( int $activity_id, int $participant_id ): string
  {
    $value          = '';
    $mod_attendance = new Attendance();

    // Obtenemos la lista de asistencia
    $attendance = $mod_attendance->GetAttendanceDetails( $activity_id, $participant_id );
    if( empty( $attendance ) )
      return '';

    $participant = $attendance[0]['participant'][0];
    $attendance  = $attendance[0]['attendance'];

    // Check-in
    if( empty( $attendance['checkin_datetime'] ) )
    {
      $checkin_input = '<button class="btn-checkin bg-green-500 text-white px-3 py-1 rounded-lg" data-pid2="' . $participant['participant_id2'] . '">
        ' . pl_label( 'mark_attendance' ) . '
      </button>';
    }
    else
    {
      $checkin_input = '<input type="datetime-local" class="checkin-input w-full border px-2 py-1 rounded" 
        value="' . date( 'Y-m-d\TH:i', strtotime( $attendance['checkin_datetime'] ) ) . '" 
        data-pid2="' . $participant['participant_id2'] . '">';
    }

    // Check-out
    if( empty( $attendance['checkout_datetime'] ) )
    {
      $checkout_input = '
        <button class="btn-checkout bg-red-500 text-white px-3 py-1 rounded-lg" data-pid2="' . $participant['participant_id2'] . '">
          ' . pl_label( 'mark_attendance' ) . '
        </button>
      ';
    }
    else
    {
      $checkout_input = '
        <input type="datetime-local" class="checkout-input w-full border px-2 py-1 rounded" 
        value="' . date( 'Y-m-d\TH:i', strtotime( $attendance['checkout_datetime'] ) ) . '" 
        data-pid2="' . $participant['participant_id2'] . '">';
    }

    // Definimos las celdas
    $cells = [
        'participant_name'  => new TableCell( $participant['participant_name'] )
      , 'checkin_datetime'  => new TableCell( $checkin_input )
      , 'checkout_datetime' => new TableCell( $checkout_input )
      , 'actions'           => new TableCell( '', ['class' => 'text-center w-10'] )
    ];

    // Añadimos la fila
    $row = new TableRow( $cells, [
        'id'    => 'row-' . $participant['participant_id2']
      , 'class' => 'hover:bg-gray-100'
    ] );

    // Retornamos la fila convertida a HTML
    return $row->html();
  }

  /**
   * Registra la hora de check-in o check-out de un participante.
   * 
   * @param array $fields Contiene `id2` (participant_id2), `type` (checkin/checkout), `datetime`.
   * @return array Respuesta con resultado, mensaje y actualización de fila.
   */
  public function ajax_update_attendance( array $fields ): array
  {
    $db       = new pl_model();
    $result   = 0;
    $message  = '';
    $redirect = '';
    $elements = [];

    $mod_participants = new Participants();
    $mod_activities   = new Activities();

    do
    {
      // --------------------------------------------------------------------------------------------------------------
      // Verificación de campos
      // --------------------------------------------------------------------------------------------------------------

      // Verificamos que el POST contiene todos los campos requeridos
      $required_fields = [
          'pid2'
        , 'aid2'
        , 'type'
        , 'datetime'
      ];

      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          $message = pl_label( 'required_field' ) . ': ' . $required_field;
          break 2;
        }
      }

      // Capturamos los datos del participante
      $participant = $mod_participants->GetRow( $fields['pid2'] );
      if( empty( $participant ) )
        break;

      // Capturamos los datos de la actividad
      $activity = $mod_activities->GetRow( $fields['aid2'] );
      if( empty( $activity ) )
        break;

      // Dependiendo del tipo, modificamos una columna u otra
      $column = ( $fields['type'] === 'checkin' )
        ? 'checkin_datetime'
        : 'checkout_datetime';

      // Actualizar la asistencia
      $sql = '
        update ' . DB_PROJECT . '.attendance set
          ' . $column . ' = "' . $db->esc( $fields['datetime'] ) . '"
        where
          participant_id = "' . $db->esc( $participant[0]['participant_id'] ) . '" and
          activity_id = ' . $activity['activity_id'] . '
      ';
      $db->pl_query( $sql );

      // Recargar la fila de la tabla
      $html = $this->table_row_attendance( $activity['activity_id'], $participant[0]['participant_id'] );
      $elements = [
        ['selector' => '#row-' . $fields['pid2'], 'method_name' => 'update', 'value' => $html]
      ];

      $result = 1;
      break;

    } while( false );

    return [
        'result'   => $result
      , 'message'  => $message
      , 'redirect' => $redirect
      , 'elements' => $elements
    ];
  }
}

?>