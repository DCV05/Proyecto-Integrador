<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class ActivityController
{
  /**
   * @var string|null $activity_id2 Identificador opcional de la actividad.
   */
  public string|null $activity_id2;

  /**
   * @var array $activity Datos de la actividad.
   */
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
    $activity = $mod_activities->GetRow( $this->activity_id2 );
    if( empty( $activity ) )
      pl_redirect( '/activities' );
    $this->activity = $activity[0];

    return;
  }

  public function __sleep(): array
  {
    return ['activity_id2', 'activity'];
  }

  public function content(): string
  {
    global $role;
    $value = '';

    $value = $role == 0
      ? $this->table_participants()
      : $this->table_groups();

    return $value;
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

        <div class="flex gap-3 items-center">
          <span class="p-tag-blue h-fit">
            ' . date( 'd/m/y | H:i', strtotime( $this->activity['activity_datetime_start'] ) )  . ' - '
              . date( 'H:i', strtotime( $this->activity['activity_datetime_end'] ) )            . '
          </span>

          ' . $this->attendance_list_link() . '
        </div>
      </div>
    ';

    return $value;
  }

  /**
   * Genera la tabla de participantes en la actividad.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_participants(): string
  {
    global $role;
    $value                  = '';
    $mod_groups             = new Groups();
    $mod_group_participants = new GroupParticipants();

    // Buscamos los participantes relacionados con esta actividad
    $groups = $mod_groups->GetRows();

    // Tabla de Participantes
    $table = new Table( ['id' => 'participants_table', 'class' => 'p-table', 'data-activity' => $this->activity['activity_id2']] );
    $table->addColumn( 'participant_name'         , new TableColumn( pl_label( 'participant_name' )         , ['id' => 'p_name_col'] ) );
    $table->addColumn( 'participant_special_needs', new TableColumn( pl_label( 'participant_special_needs' ), ['id' => 'p_special_needs_col'] ) );
    $table->addColumn( 'participant_allergies'    , new TableColumn( pl_label( 'allergies' )                , ['id' => 'p_allergies_col'] ) );

    // Iteramos cada participante relacionado con la actividad
    foreach( $groups as $group )
    {
      // Capturo los participantes del grupo
      $participants = $mod_group_participants->GetRow( $group['group_id'] );
      foreach( $participants as $participant )
      {
        // Formateamos los datos si son largos
        $participant['participant_allergies'] = empty( $participant['participant_allergies'] ) 
          ? pl_label( 'no_allergies' ) 
          : $participant['participant_allergies'];

        $participant['participant_special_needs'] = empty( $participant['participant_special_needs'] ) 
          ? ''
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
    }
    
    $value = $table->html();
    return $value;
  }

  /**
   * Genera una fila en la tabla de participantes en la actividad.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_row_participants( int $participant_id ): string
  {
    // Capturamos los datos del participante solicitado
    $participant = ( new Participants() )->GetRow( $participant_id )[0];
  
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

  public function attendance_list_link(): string
  {
    global $role;
    $value = '';

    if( $role == 1 )
    {
      $value .= '
        <a href="/monitor/attendance?aid2=' . $this->activity_id2 . '" class="p-button w-fit">
          <i class="icon">list</i>
          <span>' . pl_label( 'roll_call' ) . '</span>
        </a>
      ';
    }

    return $value;
  }

  /**
   * Genera la tabla de grupos en la actividad.
   * 
   * @return string HTML de la tabla de grupos.
   */
  public function table_groups(): string
  {
    global $role;

    $value            = '';
    $mod_groups       = new Groups();
    $mod_user_details = new UserDetails();

    // Buscamos los grupos relacionados con esta actividad
    $groups = $mod_groups->GetRows();

    // Tabla de Grupos
    $table = new Table( ['id' => 'groups_table', 'class' => 'p-table', 'data-activity' => $this->activity['activity_id2']] );
    $table->addColumn( 'group_name'  , new TableColumn( pl_label( 'group_name' )  , ['id' => 'g_name_col'] ) );
    $table->addColumn( 'monitor_name', new TableColumn( pl_label( 'monitor_name' ), ['id' => 'g_monitor_col'] ) );

    // Iteramos cada grupo relacionado con la actividad
    foreach( $groups as $group )
    {
      // Buscamos el monitor relacionado
      $monitor = $mod_user_details->GetRowsUser( $group['monitor_id'] )[0];

      // Definimos las celdas
      $cells = [
          'group_name'   => new TableCell( pl_label( 'group' ) . ' ' . ( $group['group_id'] + 1 ) )
        , 'monitor_name' => new TableCell( $monitor['user_name'], ['class' => 'max-w-[14rem]'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'    => 'row-' . $group['group_id2']
        , 'class' => 'hover:bg-gray-100'
      ] ) );
    }
    
    return $table->html();
  }

  /**
   * Genera una fila en la tabla de grupos en la actividad.
   * 
   * @return string HTML de la tabla de grupos.
   */
  public function table_row_groups( int $group_id ): string
  {
    // Capturamos los datos del grupo solicitado
    $group = ( new Groups() )->GetRow( $group_id )[0];
    
    // Formateamos los datos si son largos
    $group['monitor_relacionado'] = empty( $group['monitor_relacionado'] )
      ? pl_label( 'no_monitor' )
      : substr( $group['monitor_relacionado'], 0, 100 ) . '...';

    // Definimos las celdas
    $cells = [
        'group_name'          => new TableCell( $group['group_name'] )
      , 'monitor_relacionado' => new TableCell( $group['monitor_relacionado'], ['class' => 'max-w-[14rem]'] )
    ];

    // Añadimos la fila
    $row = new TableRow( $cells, [
        'id'    => 'row-' . $group['group_id2']
      , 'class' => 'hover:bg-gray-100'
    ] );

    return $row->html();
  }
}

?>