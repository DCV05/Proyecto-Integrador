<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class GroupController
{
  public array $group;
  public string $group_id2;

  public function index(): void
  {
    // Control de seguridad
    app_security();
    $mod_groups = new Groups();

    // Capturamos el id2 del grupo
    $this->group_id2 = pl_get( 'gid2', null );
    if( !$this->group_id2 )
      pl_redirect( '/groups' );

    // Buscamos en la DB la actividad. En caso de que no exista, redirigimos al activities
    $this->group = $mod_groups->GetGroupId2( $this->group_id2 )[0];
    if( empty( $this->group ) )
      pl_redirect( '/groups' );

    return;
  }

  // --------------------------------------------------------------------------------
  // Tabla participantes
  // --------------------------------------------------------------------------------

  /**
   * Genera la tabla de participantes.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_participants( string $where = '' ): string
  {
    global $role;
    $value = '';

    // Capturamos los grupos
    $mod_groups_participants  = new GroupParticipants();
    $group_participants = $mod_groups_participants->GetRow( $this->group_id2, $where );

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'participants_table', 'class' => 'p-table'] );

    // Columnas
    $table->addColumn( 'participant_name'             , new TableColumn( pl_label( 'name' )              ) );
    $table->addColumn( 'participant_birth_date'       , new TableColumn( pl_label( 'birth_date' )        ) );
    $table->addColumn( 'participant_allergies'        , new TableColumn( pl_label( 'allergies' )         ) );
    $table->addColumn( 'participant_special_needs'    , new TableColumn( pl_label( 'special_needs' )     ) );
    $table->addColumn( 'participant_medical_treatment', new TableColumn( pl_label( 'medical_treatment' ) ) );
    $table->addColumn( 'schedule_icon'                , new TableColumn( ''                              ) );

    // Si es un admin, mostramos los iconos de borrar
    if( $role === 2 || $role === 1 )
      $table->addColumn( 'delete_icon', new TableColumn( '', ['id' => 'delete_icon']  ) );

    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $group_participants as $participant )
    {
      // Le damos formato a los campos
      if( str_word_count( $participant['participant_medical_treatment'] ) > 50 )
        $medical_treatment = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';
      else
        $medical_treatment = $participant['participant_medical_treatment'];

      // Botón de calendario
      $schedule_icon = '
        <a href="/participant?pid2=' . $participant['participant_id2'] . '" class="p-button">
          ' . app_get_svg_icon( 'schedule' ) . '
        </a>
      ';

      // Definimos las celdas
      $cells = [
          'participant_name'              => new TableCell( $participant['participant_name'] )
        , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
        , 'participant_allergies'         => new TableCell( $participant['participant_allergies'] )
        , 'participant_special_needs'     => new TableCell( $participant['participant_special_needs'] )
        , 'participant_medical_treatment' => new TableCell( $medical_treatment, ['class' => 'max-w-[14rem]'] )
        , 'schedule_icon'                 => new TableCell( $schedule_icon, ['class' => 'text-center w-12 icon-container schedule-icon'] )
      ];

      // --------------------------------------------------------------------------------
      // ADMIN
      // --------------------------------------------------------------------------------

      // Si es un admin, mostramos los iconos de borrar
      if( $role === 2 || $role === 1 )
      {
        $delete_icon = '
          <div data-type="participant" data-pid2="' . $participant['participant_id2'] . '" class="delete-icon p-button">
            ' . app_get_svg_icon( 'trash', 'black' ) . '
          </div>
        ';

        $cells['delete_icon'] = new TableCell( $delete_icon , ['class' => 'text-center w-12 icon-container'] );
      }

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $participant['participant_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row'
        , 'data-type' => 'participant_info'
        , 'data-id2'  => $participant['participant_id2']
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
    return $value;
  }

  /**
   * Genera una fila de la tabla de participantes.
   * 
   * @param string $participant_id2 ID del participante.
   * @return string HTML de la fila de la tabla de participantes.
   */
  public function table_row_participants( string $participant_id2 ): string
  {
    global $role;

    $value               = '';
    $mod_participants    = new Participants();

    // Capturamos los datos del participante
    $participant = $mod_participants->GetRow( $participant_id2 )[0];

    // Le damos formato a los campos
    if( str_word_count( $participant['participant_medical_treatment'] ) > 50 )
      $medical_treatment = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';
    else
      $medical_treatment = $participant['participant_medical_treatment'];

    // Botón de calendario
    $schedule_icon = '
      <a href="/participant?pid2=' . $participant['participant_id2'] . '" class="p-button">
        ' . app_get_svg_icon( 'schedule' ) . '
      </a>
    ';

    // Definimos las celdas
    $cells = [
        'participant_name'              => new TableCell( $participant['participant_name'] )
      , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
      , 'participant_allergies'         => new TableCell( $participant['participant_allergies'] )
      , 'participant_special_needs'     => new TableCell( $participant['participant_special_needs'] )
      , 'participant_medical_treatment' => new TableCell( $medical_treatment, ['class' => 'max-w-[14rem]'] )
      , 'schedule_icon'                 => new TableCell( $schedule_icon, ['class' => 'text-center w-12 icon-container schedule-icon'] )
    ];

    // --------------------------------------------------------------------------------
    // ADMIN
    // --------------------------------------------------------------------------------

    // Si es un admin, mostramos los iconos de borrar
    if( $role === 2 || $role === 1 )
    {
      $delete_icon = '
        <div data-type="participant" data-pid2="' . $participant['participant_id2'] . '" class="delete-icon p-button">
          ' . app_get_svg_icon( 'trash', 'black' ) . '
        </div>
      ';

      $cells['delete_icon'] = new TableCell( $delete_icon , ['class' => 'text-center w-12 icon-container'] );
    }

    // Añadimos la fila
    $table_row = new TableRow( $cells, [
        'id'        => 'row-' . $participant['participant_id2']
      , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row'
      , 'data-type' => 'participant_info'
      , 'data-id2'  => $participant['participant_id2']
    ] );

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

    do
    {
      // Filtro
      $where = ' and p.participant_name like "%' . $fields['query'] . '%"';

      // Recargamos el HTML
      $html = $this->table_participants( $where );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#participants_table', 'method_name'  => 'update' , 'value' => $html]
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
   * Obtiene y muestra un popup con los detalles del usuario seleccionado.
   * 
   * @param array $fields Contiene el identificador del usuario (`id2`).
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_participant_info( array $fields ): array
  {
    $value           = [];
    $mod_participant = new Participants();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Buscamos los datos del participante solicitado
      $participant = $mod_participant->GetRow( $fields['id2'] );
      if( empty( $participant ) )
        break;

      $participant = $participant[0];

      /*
        Array | participant
          [participant_id] => 5
          [participant_id2] => abc123
          [user_id] => 11
          [participant_name] => Carlos
          [participant_birth_date] => 2015-06-21
          [participant_allergies] => Ninguna
          [participant_special_needs] => Ninguna
          [participant_medical_treatment] => No aplica
      */

      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh]">

            <h3 class="text-2xl mb-4">' . pl_label( 'participant' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <div class="modal_content space-y-5">
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_name' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $participant['participant_name'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_birth_date' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $participant['participant_birth_date'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_allergies' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md min-h-[100px]">' . $participant['participant_allergies'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_special_needs' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md min-h-[100px]">' . $participant['participant_special_needs'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_medical_treatment' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md min-h-[100px]">' . $participant['participant_medical_treatment'] . '</div>
              </div>
            </div>

          </div>
        </div>
      ';

      // Rellenamos los objetos a actualizar
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

    return $value;
  }

  /**
   * Crea un participante
   * 
   * @param array $fields Datos del user a actualizar.
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_add_participant( array $fields ): array
  {
    $value  = [];
    $db     = new Model();

    // Inicializamos las variables de la llamada AJAX
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
      $required_fields = ['participant_select'];
      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          // Mostramos una alerta
          $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': ' . $required_field );
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // Insert
      // --------------------------------------------------------------------------------------------------------------

      // Capturamos los grupos
      $mod_groups_participants = new GroupParticipants();
      $group_participants = $mod_groups_participants->GetRow( $this->group_id2 );

      // Verificamos que el grupo tiene los participantes máximos
      if( $this->group['group_size'] === count( $group_participants ) )
      {
        // Mostramos una alerta
        $elements = app_generate_alert( true, pl_label( 'group_at_maximum_capacity' ) );
        break;
      }

      // Buscamos el participante vinculado al submit
      $mod_participants = new Participants();
      $participant = $mod_participants->GetRow( $fields['participant_select'] );
      if( empty( $participant[0] ) )
      {
        // Mostramos una alerta
        $elements = app_generate_alert( true, pl_label( 'participant_not_exists' ) );
        break;
      }
      else
      {
        $participant_id   = $participant[0]['participant_id'];
        $participant_id2  = $fields['participant_select'];
      }

      // Insertamos los datos del nuevo participante
      $sql = '
        insert into ' . DB_PROJECT . '.group_participants (
            group_id
          , participant_id
        ) values ( ?, ? )
      ';

      $params = [$this->group['group_id'], $participant_id];
      $db->pl_query_prepared( $sql, $params );

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_participants( $participant_id2 );

      // Rellenamos los objetos a actualizar
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );
      $kwargs   = ['elem' => '#row-' . $participant_id2, 'color' => 'green'];
      $elements = array_merge( $elements, [
          ['selector' => '#participants_table tbody', 'method_name' => 'append' , 'value' => $html]
        , ['selector' => '#row-' . $participant_id2 , 'method_name' => 'execute', 'func_name' => 'highlight_row', 'kwargs' => $kwargs]
        , ['selector' => '#modal', 'method_name' => 'remove']
      ] );

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'    => $result
      , 'message'   => $message
      , 'redirect'  => $redirect
      , 'elements'  => $elements
    ];

    $db->close();
    return $value;
  }

  /**
   * Borra la vinculación entre grupo y participante
   * 
   * @param array $fields Datos del participante a eliminar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_delete_participant( array $fields ): array
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
      $required_fields = ['pid2'];
      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          // Mostramos una alerta
          $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': ' . $required_field );
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // Delete
      // --------------------------------------------------------------------------------------------------------------

      // Buscamos si la actividad existe
      $participant = ( new Participants() )->GetRow( $fields['pid2'] )[0];
      if( $participant )
        $participant_id = $participant['participant_id'];
      else
      {
        $message = pl_label( 'participant_not_found' );
        break;
      }

      // Cargamos el grupo
      $mod_groups = new Groups();
      $group = $mod_groups->GetGroupId2( $this->group_id2 )[0];

      // Borramos las vinculaciones con monitores
      $sql = '
        delete from ' . DB_PROJECT . '.group_participants
        where
          participant_id = ?
      ';
      $db->pl_query_prepared( $sql, [$participant_id] );

      // Rellenamos los objetos a actualizar
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );
      $elements = array_merge( $elements, [
        ['selector' => '#row-' . $fields['pid2'], 'method_name' => 'remove']
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

  /**
   * Formulario de vinculación de usuario
   * 
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_add_participant(): array
  {
    $value = [];

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    $mod_participants = new Participants();

    do
    {
      // Buscamos los participantes sin grupo vinculado
      $where = ' where gp.group_id is null';
      $participants = $mod_participants->GetAll( $where );

      // Generamos el select
      $select = '';
      foreach( $participants as $participant_id => $participant )
      {
        $selected = $participant_id == 0 ? 'selected' : '';
        $select   .= '<option ' . $selected . ' value="' . $participant['participant_id2'] . '">' . $participant['participant_name'] . '</option>';
      }

      // Encapsulamos
      $select = '
        <select id="participant_select" name="participant_select" class="p-select">
          ' . $select . '
        </select>
      ';

      $form_content = '
        <form data-type="add_participant" class="add-participant-form modal_content space-y-5">
          ' . $select . '

          <div class="flex justify-end gap-3">
            <button type="button" class="p-button close_modal">
              <i class="icon">cancel</i>
              <span>' . pl_label( 'cancel-button' ) . '</span>
            </button>

            <button type="submit" class="p-button">
              <i class="icon">send</i>
              <span>' . pl_label( 'send-button' ) . '</span>
            </button>
          </div>
        </form>
      ';
      
      $elements = app_generate_modal( pl_label( 'add_participant' ), $form_content );

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