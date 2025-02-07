<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class AdminActivityController
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
      pl_redirect( '/admin/activities' );

    // Buscamos en la DB la actividad. En caso de que no exista, redirigimos al activities
    $this->activity = $mod_activities->GetRow( $this->activity_id2 );
    if( empty( $this->activity ) )
      pl_redirect( '/admin/activities' );

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
}

?>