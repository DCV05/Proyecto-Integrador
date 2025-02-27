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
      pl_redirect( '/activities' );

    // Buscamos en la DB la actividad. En caso de que no exista, redirigimos al activities
    $this->activity = $mod_activities->GetRow( $this->activity_id2 )[0];
    if( empty( $this->activity ) )
      pl_redirect( '/activities' );

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
      <div class="mb-6 mt-2 space-y-5">
        <h2 class="text-3xl font-semibold text-gray-900">' . $this->activity['activity_name_' . DEF_LANG] . '</h2>
        <p class="text-gray-600">' . nl2br( $this->activity['activity_description_' . DEF_LANG] ) . '</p>
        <span class="p-tag-blue h-fit">
          ' . date( 'd/m/y | H:i', strtotime( $this->activity['activity_datetime_start'] ) )  . ' - '
            . date( 'H:i', strtotime( $this->activity['activity_datetime_end'] ) )            . '
        </span>
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
    $mod_groups     = new Groups();
    $mod_attendance = new Attendance();

    // Capturamos los datos del grupo vinculado a la cuenta de la sesión
    $group = $mod_groups->GetRow( $_SESSION['app']['user']['user_id'] )[0];
    if( !$group )
      return '';

    // Capturamos los datos de su asistencia según el día de la actividad
    $attendances = $mod_attendance->GetGroupRows( $group['group_id'], $this->activity['activity_id'] );

    // Tabla de Asistencia
    $table = new Table( ['id' => 'attendance_table', 'class' => 'p-table', 'data-activity' => $this->activity['activity_id2']] );
    $table->addColumn( 'participant_name' , new TableColumn( pl_label( 'participant_name' ), ['id' => 'p_name_col'] ) );
    $table->addColumn( 'checkin_datetime' , new TableColumn( pl_label( 'checkin' )         , ['id' => 'p_checkin_col'] ) );
    $table->addColumn( 'checkout_datetime', new TableColumn( pl_label( 'checkout' )        , ['id' => 'p_checkout_col'] ) );

    // Iteramos cada registro de asistencia
    foreach( $attendances as $entry )
    {
      // Check-in
      $checkin_input = $this->app_generate_check_component(
          $entry['participant_id2']
        , 'checkin'
        , !empty( $entry['checkin_datetime'] ) ? date( 'Y-m-d\TH:i', strtotime( $entry['checkin_datetime'] ) ): null
      );

      // Check-out
      $checkout_input = $this->app_generate_check_component(
          $entry['participant_id2']
        , 'checkout'
        , !empty( $entry['checkout_datetime'] ) ? date( 'Y-m-d\TH:i', strtotime( $entry['checkout_datetime'] ) ): null
      );

      // Definimos las celdas
      $cells = [
          'participant_name'  => new TableCell( $entry['participant_name'] )
        , 'checkin_datetime'  => new TableCell( $checkin_input )
        , 'checkout_datetime' => new TableCell( $checkout_input )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $entry['participant_id2']
        , 'class'     => 'hover:bg-gray-100 table-row-link cursor-pointer'
        , 'data-href' => '/participant?pid2=' . $entry['participant_id2']
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
    $value            = '';
    $mod_groups       = new Groups();
    $mod_attendance   = new Attendance();
    $mod_participants = new Participants();

    // Capturamos los datos del grupo vinculado a la cuenta de la sesión
    $group = $mod_groups->GetRow( $_SESSION['app']['user']['user_id'] )[0];
    if( !$group )
      return '';

    // Capturamos los datos de su asistencia según el día de la actividad
    $attendance   = $mod_attendance->GetParticipantRow( $participant_id, $activity_id )[0];
    $participant  = $mod_participants->GetRow( $participant_id )[0];

    // Check-in
    $checkin_input = $this->app_generate_check_component(
        $participant['participant_id2']
      , 'checkin'
      , !empty( $entry['checkin_datetime'] ) ? date( 'Y-m-d\TH:i', strtotime( $attendance['checkin_datetime'] ) ): null
    );

    // Check-out
    $checkout_input = $this->app_generate_check_component(
        $participant['participant_id2']
      , 'checkout'
      , !empty( $entry['checkout_datetime'] ) ? date( 'Y-m-d\TH:i', strtotime( $attendance['checkout_datetime'] ) ): null
    );

    // Definimos las celdas
    $cells = [
        'participant_name'  => new TableCell( $participant['participant_name'] )
      , 'checkin_datetime'  => new TableCell( $checkin_input )
      , 'checkout_datetime' => new TableCell( $checkout_input )
    ];

    // Añadimos la fila
    $row = new TableRow( $cells, [
        'id'    => 'row-' . $participant['participant_id2']
      , 'class' => 'hover:bg-gray-100 table-row-link cursor-pointer'
      , 'data-href' => '/participant?pid2=' . $participant['participant_id2']
    ] );

    // Retornamos la fila convertida a HTML
    $value = $row->html();
    return $value;
  }

  /**
   * Registra la hora de check-in o check-out de un participante.
   * 
   * @param array $fields Contiene `id2` (participant_id2), `type` (checkin/checkout), `datetime`.
   * @return array Respuesta con resultado, mensaje y actualización de fila.
   */
  public function ajax_update_attendance( array $fields ): array
  {
    $db       = new Model();
    $result   = 0;
    $message  = '';
    $redirect = '';
    $elements = [];

    $mod_participants = new Participants();

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

      // Dependiendo del tipo, modificamos una columna u otra
      $column = ( $fields['type'] === 'checkin' )
        ? 'checkin_datetime'
        : 'checkout_datetime';

      // Actualizar la asistencia
      // Utilizamos ON DUPLICATE KEY UPDATE para actualizar el registro en caso de que ya exista
      // Utilizamos VALUES para referirnos a los valores que intentamos insertar en la sentencia INSERT
      // Utilizamos COALESCE para mantener los valores existentes en las columnas que no estamos actualizando
      $sql = '
        insert into ' . DB_PROJECT . '.attendance (
            attendance_id2
          , participant_id
          , activity_id
          , ' . $column . '
        ) values ( ?, ?, ?, ? )
         on duplicate key update
            checkin_datetime = coalesce( values( checkin_datetime ), checkin_datetime )
          , checkout_datetime = coalesce( values( checkout_datetime ), checkout_datetime )
      ';
      $params = [
          pl_random()
        , $participant[0]['participant_id']
        , $this->activity['activity_id']
        , $fields['datetime']
      ];
      
      $db->pl_query_prepared( $sql, $params, false, true );

      // Recargar la fila de la tabla
      $html = $this->table_row_attendance( $this->activity['activity_id'], $participant[0]['participant_id'] );
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

  /**
   * Genera el componente de check-in/check-out para la asistencia de un participante.
   * 
   * Si el participante aún no ha hecho check-in, muestra un botón.
   * Si ya tiene un check-in registrado, muestra un input para modificar la fecha/hora.
   *
   * @param string $participant_id2 ID del participante en formato string.
   * @param string|null $check_datetime Fecha y hora del check-in en formato string (opcional, puede ser null).
   * @return string HTML del componente generado.
   */
  public function app_generate_check_component( string $participant_id2, string $label, string|null $check_datetime = null ): string
  {
    $value = '';

    // Si el participante no ha hecho check-in, mostramos un botón
    if( empty( $checkin_datetime ) )
    {
      $value = '
        <button class="p-button btn-' . $label . '" data-pid2="' . $participant_id2 . '">
          <i class="icon">calendar_today</i>
          <span>' . $label . '</span>
        </button>
      ';
    }
    else
    {
      // Si el participante ya tiene check-in, mostramos un input con la fecha y hora
      $value = '
        <input
          type="datetime-local"
          class="p-input w-fit" 
          value="' . date( 'Y-m-d\TH:i', strtotime( $check_datetime ) ) . '" 
          data-pid2="' . $participant_id2 . '"
        >
      ';
    }
    return $value;
  }
}

?>