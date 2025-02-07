<?php declare( strict_types = 1 );

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class TutorActivityController
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
      pl_redirect( '/tutor/activities' );

    // Buscamos en la DB la actividad. En caso de que no exista, redirigimos al activities
    $this->activity = $mod_activities->GetRow( $this->activity_id2 );
    if( empty( $this->activity ) )
      pl_redirect( '/tutor/activities' );

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
   * Genera la tabla de participantes en la actividad.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_participants( array $activity = null ): string
  {
    $mod_activities_participants = new ActivitiesParticipants();
    $this->activity = $activity ?? $this->activity;

    // Buscamos los participantes relacionados con esta actividad
    $participants = $mod_activities_participants->GetActivityDetails( $this->activity['activity_id'] );

    // Tabla de Participantes
    $table = new Table( ['id' => 'participants_table', 'class' => 'table-ui', 'data-activity' => $this->activity['activity_id2']] );

    // Columnas
    $table->addColumn( 'participant_name'         , new TableColumn( pl_label( 'participant_name' )         , ['id' => 'p_name_col'] ) );
    $table->addColumn( 'participant_special_needs', new TableColumn( pl_label( 'participant_special_needs' ), ['id' => 'p_special_needs_col'] ) );
    $table->addColumn( 'participant_allergies'    , new TableColumn( pl_label( 'allergies' )                , ['id' => 'p_allergies_col'] ) );

    // Iteramos cada participante relacionado con la actividad
    foreach( $participants as $participant )
    {
      // Formateamos los datos si son largos
      $participant['participant_allergies'] = empty( $participant['participant_allergies'] ) 
        ? pl_label( 'no_allergies' ) 
        : $participant['participant_allergies'];

      $participant['participant_special_needs'] = empty( $participant['participant_special_needs'] ) 
        ? pl_label( 'no_allergies' ) 
        : $participant['participant_special_needs'];

      // Definimos las celdas
      $cells = [
          'participant_name'          => new TableCell( $participant['participant_name'] )
        , 'participant_special_needs' => new TableCell( $participant['participant_special_needs'], ['class' => 'max-w-[14rem]'] )
        , 'participant_allergies'     => new TableCell( $participant['participant_allergies'], ['class' => 'max-w-[14rem]'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'    => 'row-' . $participant['participant_id2']
        , 'class' => 'hover:bg-gray-100'
      ] ) );
    }

    // Botón para añadir participante
    $add_button = ['participant_name' => new TableCell( $this->participant_select(), ['colspan' => 3, 'class' => 'text-center'] )];
    $table->addRow( new TableRow( $add_button ) );
    
    return $table->html();
  }

  /**
   * Genera una fila en la tabla de participantes en la actividad.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_row_participants( int $participant_id ): string
  {
    $mod_participants = new Participants();
    
    // Capturamos los datos del participante solicitado
    $participant = $mod_participants->GetRow( $participant_id );
  
    // Formateamos los datos si son largos
    $participant['participant_allergies'] = empty( $participant['participant_allergies'] ) 
      ? pl_label( 'no_allergies' ) 
      : substr( $participant['participant_allergies'], 0, 100 ) . '...';

    // Definimos las celdas
    $cells = [
        'participant_name'       => new TableCell( $participant['participant_name'] )
      , 'participant_birth_date' => new TableCell( $participant['participant_birth_date'] )
      , 'participant_allergies'  => new TableCell( $participant['participant_allergies'], ['class' => 'max-w-[14rem]'] )
    ];

    // Añadimos la fila
    $row = new TableRow( $cells, [
        'id'    => 'row-' . $participant['participant_id2']
      , 'class' => 'hover:bg-gray-100'
    ] );

    return $row->html();
  }

  /**
   * Genera un select con los participantes del usuario que NO están inscritos en la actividad.
   * 
   * @return string HTML del select con los participantes disponibles.
   */
  public function participant_select(): string
  {
    $mod_participants             = new Participants();
    $mod_activities_participants  = new ActivitiesParticipants();

    // ---------------------------–---------------------------–---------------------------–
    // Obtención de datos
    // ---------------------------–---------------------------–---------------------------–

    // Obtenemos todos los participantes del tutor
    $all_participants         = $mod_participants->GetRows( $_SESSION['app']['user']['user_id'] );
    $registered_participants  = $mod_activities_participants->GetActivityDetails( $this->activity['activity_id'] );

    // Extraemos los participant_id2 de los ya inscritos
    $registered_ids = array_column( $registered_participants, 'participant_id2' );

    // Filtramos los participantes que NO están en la actividad
    $available_participants = array_filter( $all_participants, function( $participant ) use ( $registered_ids ): bool {
      return !in_array( $participant['participant_id2'], $registered_ids );
    } );

    // Si no hay participantes disponibles, mostramos una alerta
    if( empty( $available_participants ) )
      return '';

    // ---------------------------–---------------------------–---------------------------–
    // Generamos el select
    // ---------------------------–---------------------------–---------------------------–

    $select_html = '<select id="participant_select" class="border px-4 py-2 rounded-lg w-full">';
    foreach( $available_participants as $participant )
      $select_html .= '<option value="' . $participant['participant_id2'] . '">' . $participant['participant_name'] . '</option>';
    
    $select_html .= '</select>';

    // Botón para agregar participante
    $select_html .= '
      <button id="btn-add-participant" class="bg-blue-500 text-white px-4 py-2 rounded-lg text-center hover:bg-blue-600 w-full">
        ' . pl_label( 'add_participant' ) . '
      </button>
    ';

    return $select_html;
  }

  /**
   * Agrega un participante a la actividad.
   * 
   * @param array $fields Contiene `participant_id2` y `activity_id2`.
   * @return array Respuesta con resultado, mensaje y actualización de la tabla.
   */
  public function ajax_add_participant( array $fields ): array
  {
    $db               = new pl_model();
    $mod_participants = new Participants();
    $mod_activities   = new Activities();

    $result   = 0;
    $message  = '';
    $redirect = '';
    $elements = [];

    do
    {
      // --------------------------------------------------------------------------------------------------------------
      // Verificación de campos
      // --------------------------------------------------------------------------------------------------------------

      // Verificamos que el POST contiene todos los campos requeridos
      $required_fields = [
          'pid2'
        , 'aid2'
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

      // Validamos si la actividad existe
      $activity = $mod_activities->GetRow( $fields['aid2'] );
      if( empty( $activity ) )
      {
        $message = pl_label( 'activity_not_found' );
        break;
      }


      // Validamos si el participante existe y pertenece al tutor actual
      $participant = $mod_participants->GetRow( $fields['pid2'] );
      if( empty( $participant ) )
      {
        $message = pl_label( 'participant_not_found' );
        break;
      }

      // Insertamos el nuevo registro
      $sql = '
        insert into ' . DB_PROJECT . '.activities_participants (
            activity_id
          , participant_id
        ) values (
            "' . $db->esc( $activity['activity_id'] ) . '"
          , "' . $db->esc( $participant[0]['participant_id'] ) . '"
        )
      ';
      $db->pl_query( $sql );

      // Recargamos la tabla de participantes
      $html = $this->table_participants( $activity );
      $elements = [
        ['selector' => '#participants_table', 'method_name' => 'update', 'value' => $html]
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