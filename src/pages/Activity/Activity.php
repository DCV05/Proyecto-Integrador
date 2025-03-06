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

  public function button_add_group(): string
  {
    global $role;
    $value = '';

    if( $role === 2 )
    {
      $value = '
        <button type="button" id="btn-add-group" class="p-button">
          <i class="icon">add</i>
          <span class="hidden md:block">' . pl_label( 'add-group' ) . '</span>
          <span class="block md:hidden">' . pl_label( 'add' ) . '</span>
        </button>
      '; 
    }

    return $value;
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
    $table = new Table( ['id' => 'participants_table', 'class' => 'p-table', 'data-activity' => $this->activity['activity_id2'], 'role' => 'table'] );
    $table->addColumn( 'participant_name'         , new TableColumn( pl_label( 'participant_name' )         , ['scope' => 'col'] ) );
    $table->addColumn( 'participant_special_needs', new TableColumn( pl_label( 'participant_special_needs' ), ['scope' => 'col'] ) );
    $table->addColumn( 'participant_allergies'    , new TableColumn( pl_label( 'allergies' )                , ['scope' => 'col'] ) );

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
          , 'role'  => 'row'
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
      , 'role'  => 'row'
    ] );

    return $row->html();
  }

  public function attendance_list_link(): string
  {
    global $role;
    $value = '';

    $mod_groups           = new Groups();
    $mod_group_activities = new GroupActivities();

    // Buscamos si el monitor está relacionado con esta actividad
    $group = $mod_groups->GetRow( $_SESSION['app']['user']['user_id'] );
    if( empty( $group[0] ) )
      return '';
    else
      $group_id = $group[0]['group_id'];

    // Buscamos las actividades relacionadas
    $group_activities = $mod_group_activities->GetRow( $group_id );
    $found = false;
    foreach( $group_activities as $activity )
    {
      if( $this->activity_id2 === $activity['activity_id2'] )
      {
        $found = true;
        break;
      }
    }

    // Solo si se encontró coincidencia y el rol es 1 (monitor), añadimos el enlace
    if( $found && $role == 1 )
    {
      $value = '
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
  public function table_groups( string $where = '' ): string
  {
    global $role;

    $value                  = '';
    $mod_group_activities   = new GroupActivities();
    $mod_user_details       = new UserDetails();

    // Capturamos los grupos
    $groups = $mod_group_activities->GetRows();

    // Tabla de Grupos
    $table = new Table( ['id' => 'groups_table', 'class' => 'p-table', 'data-activity' => $this->activity['activity_id2'], 'role' => 'table'] );
    $table->addColumn( 'group_name'  , new TableColumn( pl_label( 'group_name' )  , ['scope' => 'col'] ) );
    $table->addColumn( 'monitor_name', new TableColumn( pl_label( 'monitor_name' ), ['scope' => 'col'] ) );

    // Si es un admin, mostramos los iconos de editar y borrar
    if( $role === 2 || $role === 1 )
      $table->addColumn( 'delete_icon', new TableColumn( '', ['id' => 'delete_icon', 'scope' => 'col'] ) );

    // Iteramos cada grupo relacionado con la actividad
    foreach( $groups as $group )
    {
      // Buscamos el monitor relacionado
      $monitor = $mod_user_details->GetRowsUser( $group['monitor_id'] )[0];

      // Definimos las celdas
      $cells = [
          'group_name'   => new TableCell( $group['group_name'] )
        , 'monitor_name' => new TableCell( $monitor['user_name'], ['class' => 'max-w-[14rem]'] )
      ];

      // --------------------------------------------------------------------------------
      // ADMIN
      // --------------------------------------------------------------------------------

      // Si es un admin, mostramos los iconos de editar y borrar
      if( $role === 2 || $role === 1 )
      {
        $delete_icon = '
          <div data-type="group" data-gid2="' . $group['group_id2'] . '" class="delete-icon p-button">
            ' . app_get_svg_icon( 'trash', 'black' ) . '
          </div>
        ';

        $cells['delete_icon'] = new TableCell( $delete_icon , ['class' => 'text-center w-12 icon-container'] );
      }

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $group['group_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
        , 'data-href' => '/group?gid2=' . $group['group_id2']
        , 'role'      => 'row'
      ] ) );
    }
    
    $value = $table->html();
    return $value;
  }

  /**
   * Genera una fila en la tabla de grupos en la actividad.
   * 
   * @return string HTML de la tabla de grupos.
   */
  public function table_row_groups( string $group_id2 ): string
  {
    global $role;
    $mod_groups       = new Groups();
    $mod_user_details = new UserDetails();

    // Capturamos los datos del grupo
    $group = $mod_groups->GetGroupGID( $group_id2 )[0];
    if( empty( $group ) )
      return '';

    // Capturamos los datos del monitor vinculado al grupo
    if( $group['monitor_id'] > 0 )
    {
      $monitor      = $mod_user_details->GetRowsUser( $group['monitor_id'] )[0];
      $monitor_name = $monitor['user_name'];
    }
    else
      $monitor_name = '-';

    // Definimos las celdas
    $cells = [
        'group_name'         => new TableCell( $group['group_name'] )
      , 'monitor_name'       => new TableCell( $monitor_name )
    ];

    // --------------------------------------------------------------------------------
    // ADMIN
    // --------------------------------------------------------------------------------

    // Si es un admin, mostramos los iconos de editar y borrar
    if( $role === 2 || $role === 1 )
    {
      $delete_icon = '
        <div data-type="group" data-gid2="' . $group['group_id2'] . '" class="delete-icon p-button">
          ' . app_get_svg_icon( 'trash', 'black' ) . '
        </div>
      ';

      $cells['delete_icon'] = new TableCell( $delete_icon, ['class' => 'text-center w-12 icon-container'] );
    }

    // Creamos la fila con `TableRow`
    $row = new TableRow( $cells, [
        'id'    => 'row-' . $group['group_id2']
      , 'class' => 'hover:bg-gray-100'
      , 'role'  => 'row'
    ] );

    // Retornamos la fila convertida a HTML
    return $row->html();
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

    do
    {
      // Filtro
      $where = ' where group_name like "%' . $fields['query'] . '%"';

      // Recargamos el HTML
      $html = $this->table_groups( $where );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#groups_table', 'method_name'  => 'update' , 'value' => $html]
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
   * Obtiene y muestra un popup con el formulario para añadir un grupo.
   * 
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_add(): array
  {
    $value = [];

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    $db = new Model( DB_PROJECT );

    do
    {
      // Capturamos los monitores sin grupo asignado
      $sql = '
        select
          g.*
        from groups g
        left join group_activities ga on g.group_id = ga.group_id
        where
          ga.group_id is null
      ';
      $groups = $db->pl_query_prepared( $sql, [], true );

      // Generamos el select
      $select = '';
      foreach( $groups as $group_id => $group )
      {
        $selected = $group_id == 0 ? 'selected' : '';
        $select   .= '<option ' . $selected . ' value="' . $group['group_id2'] . '">' . $group['group_name'] . '</option>';
      }

      // Encapsulamos
      $select = '
        <select id="group_select" name="group_select" class="p-select">
          ' . $select . '
        </select>
      ';

      // --------------------------------------------------------------------------------------------------------------
      // Formulario
      // --------------------------------------------------------------------------------------------------------------
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">

            <h3 class="text-2xl mb-4">' . pl_label( 'add_group' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <form data-type="add-group-form" class="add-group-form modal_content space-y-5">
              ' . $select . '

              <div class="flex justify-end">
                <button type="submit" class="p-button">
                  <i class="icon">send</i>
                  <span>' . pl_label( 'send' ) . '</span>
                </button>
              </div>
            </form>

          </div>
        </div>
      ';

      // --------------------------------------------------------------------------------------------------------------
      // Rellenamos los objetos a actualizar
      // --------------------------------------------------------------------------------------------------------------
      $elements = [
          ['selector' => 'body'       , 'method_name' => 'append', 'value' => $html]
        , ['selector' => '.card_modal', 'method_name' => 'css'   , 'value' => 'flex', 'css' => 'display']
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

    $db->close();
    return $value;
  }

  /**
   * Añade un grupo.
   * 
   * @param array<string,string> $fields Datos del grupo a crear.
   * @return array<string,mixed> Respuesta con resultado, mensaje y elementos para actualizar el DOM.
   */
  public function ajax_add_group( array $fields ): array
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
      $required_fields = ['group_select'];
      foreach( $required_fields as $required_field )
      {
        if( !array_key_exists( $required_field, $fields ) )
        {
          $message = pl_label( 'required_field' ) . ': ' . $required_field;
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // Insert
      // --------------------------------------------------------------------------------------------------------------

      // Verificamos que exista el grupo
      $group_id2 = $fields['group_select'];
      $sql = '
        select
          *
        from ' . DB_PROJECT . '.groups
        where
          group_id2 = ?
      ';
      $result = $db->pl_query_prepared( $sql, [$group_id2], true );
      if( !$result )
      {
        // Mostramos una alerta
        $elements = app_generate_alert( true, pl_label( 'group_already_exists' ) );
        break;
      }

      // Generamos un identificador único para el grupo
      $sql = '
        insert into ' . DB_PROJECT . '.group_activities (
            activity_id
          , group_id
        ) values ( ?, ? )
      ';
      $params = [
          $this->activity['activity_id']
        , $result[0]['group_id']
      ];
      $db->pl_query_prepared( $sql, $params );

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_groups( $group_id2 );

      // Rellenamos los objetos a actualizar
      $kwargs   = ['elem' => '#row-' . $group_id2, 'color' => 'green'];
      $elements = [
          ['selector' => '#groups_table tbody tr:last', 'method_name'  => 'insertBefore', 'value' => $html]
        , ['selector' => '#row-' . $group_id2, 'method_name' => 'execute', 'func_name' => 'highlight_row', 'kwargs' => $kwargs]
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

    $db->close();
    return $value;
  }

    /**
   * Borra los datos de un grupo.
   * 
   * @param array $fields Datos del grupo a eliminar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_delete_group( array $fields ): array
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
      $required_fields = ['gid2'];
      foreach( $required_fields as $required_field )
      {
        if( !array_key_exists( $required_field, $fields ) )
        {
          $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': ' . $required_field );
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // Delete
      // --------------------------------------------------------------------------------------------------------------

      // Buscamos si el grupo existe
      $group = ( new Groups() )->GetGroupGID( $fields['gid2'] )[0];
      if( $group )
        $group_id = $group['group_id'];
      else
      {
        $elements = app_generate_alert( true, pl_label( 'group_not_found' ) );
        break;
      }

      // Borramos el grupo
      $sql = '
        delete from ' . DB_PROJECT . '.group_activities
        where
          group_id = ? and
          activity_id = ?
      ';
      $db->pl_query_prepared( $sql, [$group_id, $this->activity['activity_id']] );

      // --------------------------------------------------------------------------------------------------------------
      // Generamos la alerta de éxito
      // --------------------------------------------------------------------------------------------------------------
      $elements = app_generate_alert( false, pl_label( 'delete_success' ) );

      // Rellenamos los objetos a actualizar
      $elements = array_merge( $elements, [
        ['selector' => '#row-' . $fields['gid2'], 'method_name' => 'remove']
      ] );

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

    $db->close();
    return $value;
  }

}

?>