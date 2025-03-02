<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class ParticipantsController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    return;
  }

  // --------------------------------------------------------------------------------
  // Tabla participantes
  // --------------------------------------------------------------------------------

  /**
   * Genera la tabla de participantes asociados al usuario autenticado.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_participants( string $where = '' ): string
  {
    global $role;

    $value            = '';
    $mod_participants = new Participants();

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    if( $role === 0 )
      $participants = $mod_participants->GetRows( $_SESSION['app']['user']['user_id'] );
    else
      $participants = $mod_participants->GetAll( $where );

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'participants_table', 'class' => 'p-table'] );

    // Columnas
    $table->addColumn( 'participant_name'             , new TableColumn( pl_label( 'name' )             , ['id' => 'p_name_col']          ) );
    $table->addColumn( 'participant_birth_date'       , new TableColumn( pl_label( 'birth_date' )       , ['id' => 'p_birth_date_col']    ) );
    $table->addColumn( 'participant_allergies'        , new TableColumn( pl_label( 'allergies' )        , ['id' => 'p_allergies_col']     ) );
    $table->addColumn( 'participant_special_needs'    , new TableColumn( pl_label( 'special_needs' )    , ['id' => 'p_special_needs_col'] ) );
    $table->addColumn( 'participant_medical_treatment', new TableColumn( pl_label( 'medical_treatment' ), ['id' => 'p_medical_treatment'] ) );
    $table->addColumn( 'family_icon'                  , new TableColumn( ''                             , ['id' => 'family_icon']         ) );
    $table->addColumn( 'schedule_icon'                , new TableColumn( ''                             , ['id' => 'schedule_icon']       ) );

    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $participants as $participant )
    {
      // Le damos formato a los campos
      if( str_word_count( $participant['participant_medical_treatment'] ) > 50 )
        $medical_treatment = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';
      else
        $medical_treatment = $participant['participant_medical_treatment'];

      // Botón para ver los padres
      $family_icon = '
        <div data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="family-icon p-button">
          ' . app_get_svg_icon( 'family' ) . '
        </div>
      ';

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
        , 'family_icon'                   => new TableCell( $family_icon, ['class' => 'text-center w-12 icon-container family-icon'] )
        , 'schedule_icon'                 => new TableCell( $schedule_icon, ['class' => 'text-center w-12 icon-container schedule-icon'] )
      ];

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
   * Genera la tabla de usuarios relacionados al usuario autenticado.
   * 
   * @return string HTML de la tabla de usuarios.
   */
  public function table_tutors( int $user_id ): string
  {
    $value            = '';
    $mod_user_details = new UserDetails();

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    $users = $mod_user_details->GetRowsUser( $user_id );

    /*
      Array | account
        [detail_id] => 4
        [user_id] => 11
        [user_name] => Daniel
        [user_email] => user1@example.com
        [user_dni] => 34213213
        [user_phone_number] => 644753740
    */

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'users_table', 'class' => 'p-table'] );
    $table->addColumn( 'user_name'        , new TableColumn( pl_label( 'name' )         , ['id' => 'name_col']          ) );
    $table->addColumn( 'user_email'       , new TableColumn( pl_label( 'email' )        , ['id' => 'email_col']         ) );
    $table->addColumn( 'user_relationship', new TableColumn( pl_label( 'relationship' ) , ['id' => 'relationship_col']  ) );
    $table->addColumn( 'user_dni'         , new TableColumn( pl_label( 'dni' )          , ['id' => 'dni_col']           ) );
    $table->addColumn( 'user_phone_number', new TableColumn( pl_label( 'phone_number' ) , ['id' => 'phone_number_col']  ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $users as $user )
    {
      // Definimos las celdas
      $cells = [
          'user_name'         => new TableCell( $user['user_name'] )
        , 'user_email'        => new TableCell( $user['user_email'] )
        , 'user_relationship' => new TableCell( $user['user_relationship'] )
        , 'user_dni'          => new TableCell( $user['user_dni'] )
        , 'user_phone_number' => new TableCell( $user['user_phone_number'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $user['detail_id2']
        , 'class'     => 'hover:bg-gray-100'
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
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
      // Recargamos el HTML de la fila actualizada
      $where = ' where p.participant_name like "%' . $fields['query'] . '%"';
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

      // --------------------------------------------------------------------------------------------------------------
      // Construcción del contenido del modal
      // --------------------------------------------------------------------------------------------------------------
      $content = '
        <div class="modal_content space-y-5">
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_name' ) . '</label>
            <div class="p-input">' . $participant['participant_name'] . '</div>
          </div>
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_birth_date' ) . '</label>
            <div class="p-input">' . $participant['participant_birth_date'] . '</div>
          </div>
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_allergies' ) . '</label>
            <div class="p-input min-h-[36px]">' . $participant['participant_allergies'] . '</div>
          </div>
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_special_needs' ) . '</label>
            <div class="p-input min-h-[36px]">' . $participant['participant_special_needs'] . '</div>
          </div>
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_medical_treatment' ) . '</label>
            <div class="p-input min-h-[36px]">' . $participant['participant_medical_treatment'] . '</div>
          </div>
        </div>
      ';

      // --------------------------------------------------------------------------------------------------------------
      // Generamos el modal con `app_generate_modal`
      // --------------------------------------------------------------------------------------------------------------
      $elements = app_generate_modal( pl_label( 'participant' ), $content );
      
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
   * Obtiene y muestra un popup con los detalles de la familia del usuario seleccionado.
   * 
   * @param array $fields Contiene el identificador del usuario (`id2`).
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_family( array $fields ): array
  {
    $value            = [];
    $mod_participants = new Participants();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Buscamos el usuario vinculado
      $user = $mod_participants->GetUserIdByParticipant( $fields['id2'] );
      if( empty( $user[0]['user_id'] ) )
      {
        // Mostramos una alerta
        $elements = app_generate_alert( true, pl_label( 'user_not_exists' ) );
        break;
      }
      else
        $user_id = $user[0]['user_id'];

      // --------------------------------------------------------------------------------------------------------------
      // Construcción del contenido del modal
      // --------------------------------------------------------------------------------------------------------------
      $content = $this->table_tutors( $user_id );

      // --------------------------------------------------------------------------------------------------------------
      // Generamos el modal con `app_generate_modal`
      // --------------------------------------------------------------------------------------------------------------
      $elements = app_generate_modal( pl_label( 'tutors' ), $content );
      
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