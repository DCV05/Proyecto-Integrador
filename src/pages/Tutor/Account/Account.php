<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class TutorAccountController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();

    return;
  }

  // --------------------------------------------------------------------------------
  // Tabla usuarios
  // --------------------------------------------------------------------------------

  /**
   * Genera la tabla de usuarios relacionados al usuario autenticado.
   * 
   * @return string HTML de la tabla de usuarios.
   */
  public function table_users(): string
  {
    $value             = '';
    $mod_user_detailss = new UserDetails();

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    $users = $mod_user_detailss->GetRowsUser( $_SESSION['app']['user']['user_id'] );

    /*
      Array | account
        [detail_id] => 4
        [user_id] => 11
        [user_name] => Daniel
        [user_email] => tutor1@example.com
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
    $table->addColumn( 'edit_icon'        , new TableColumn( ''                         , ['id' => 'edit_icon']         ) );
    $table->addColumn( 'delete_icon'      , new TableColumn( ''                         , ['id' => 'delete_icon']       ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $users as $user )
    {
      $edit_icon = '
        <div data-type="user" data-id2="' . $user['detail_id2'] . '" class="edit-icon p-button">
          ' . app_get_svg_icon( 'pen' ) . '
        </div>
      ';

      $delete_icon = '
        <div data-type="user" data-aid2="' . $user['detail_id2'] . '" class="delete-icon p-button">
          ' . app_get_svg_icon( 'trash' ) . '
        </div>
      ';

      // Definimos las celdas
      $cells = [
          'user_name'         => new TableCell( $user['user_name'] )
        , 'user_email'        => new TableCell( $user['user_email'] )
        , 'user_relationship' => new TableCell( $user['user_relationship'] )
        , 'user_dni'          => new TableCell( $user['user_dni'] )
        , 'user_phone_number' => new TableCell( $user['user_phone_number'] )
        , 'edit_icon'         => new TableCell( $edit_icon, ['class' => 'text-center w-10 icon-container'] )
        , 'delete_icon'       => new TableCell( $delete_icon, ['class' => 'text-center w-10 icon-container'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $user['detail_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row'
        , 'data-type' => 'user_info'
        , 'data-id2'  => $user['detail_id2']
      ] ) );
    }

    // Convertimos la tabla a HTML
    $table_html = $table->html();
  
    // --------------------------------------------------------------------------
    // Encapsulamos
    // --------------------------------------------------------------------------

    $value = '
      <h2 class="subtitle font-semibold my-4">' . pl_label( 'users' ) . '</h2>
      ' . $table_html . '
    ';  

    return $value;
  }

  /**
   * Genera una fila de tabla (`TableRow`) para un usuario específico.
   * 
   * @param string $detail_id2 Identificador del usuario (`detail_id2`).
   * @return string Fila de tabla con los datos del usuario, o `null` si no existe.
   */
  public function table_row_users( string $detail_id2 ): string
  {
    // Capturamos los datos del usuario
    $user = ( new UserDetails() )->GetRow( $detail_id2 )[0];
    if( empty( $user ) )
      return '';

    /*
      Array | user
        [detail_id] => 4
        [user_id] => 11
        [user_name] => Daniel
        [user_email] => tutor1@example.com
        [user_dni] => 34213213
        [user_phone_number] => 644753740
    */

    $edit_icon = '
      <div data-type="user" data-id2="' . $detail_id2 . '" class="edit-icon p-button">
        ' . app_get_svg_icon( 'pen' ) . '
      </div>
    ';

    $delete_icon = '
      <div data-type="user" data-aid2="' . $user['detail_id2'] . '" class="delete-icon p-button">
        ' . app_get_svg_icon( 'trash' ) . '
      </div>
    ';

    // Definimos las celdas
    $cells = [
        'user_name'         => new TableCell( $user['user_name'] )
      , 'user_email'        => new TableCell( $user['user_email'] )
      , 'user_relationship' => new TableCell( $user['user_relationship'] )
      , 'user_dni'          => new TableCell( $user['user_dni'] )
      , 'user_phone_number' => new TableCell( $user['user_phone_number'] )
      , 'edit_icon'         => new TableCell( $edit_icon, ['class' => 'text-center w-10 icon-container'] )
      , 'delete_icon'       => new TableCell( $delete_icon, ['class' => 'text-center w-10 icon-container'] )
    ];

    // Retornamos la fila de tabla generada
    $table_row = new TableRow( $cells, [
        'id'        => 'row-' . $detail_id2
      , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row'
      , 'data-type' => 'user_info'
      , 'data-id2'  => $detail_id2
    ] );
    return $table_row->html();
  }

  // --------------------------------------------------------------------------------
  // Tabla participantes
  // --------------------------------------------------------------------------------

  /**
   * Genera la tabla de participantes asociados al usuario autenticado.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_participants(): string
  {
    $value = '';

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    $participants = ( new Participants() )->GetRows( $_SESSION['app']['user']['user_id'] );

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'participants_table', 'class' => 'p-table'] );
    $table->addColumn( 'participant_name'             , new TableColumn( pl_label( 'name' )             , ['id' => 'p_name_col']          ) );
    $table->addColumn( 'participant_birth_date'       , new TableColumn( pl_label( 'birth_date' )       , ['id' => 'p_birth_date_col']    ) );
    $table->addColumn( 'participant_medical_treatment', new TableColumn( pl_label( 'medical_treatment' ), ['id' => 'p_medical_treatment'] ) );
    $table->addColumn( 'schedule_icon'                , new TableColumn( ''                             , ['id' => 'schedule_icon']       ) );
    $table->addColumn( 'edit_icon'                    , new TableColumn( ''                             , ['id' => 'edit_icon']           ) );
    $table->addColumn( 'delete_icon'                  , new TableColumn( ''                             , ['id' => 'delete_icon']         ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $participants as $participant )
    {
      // Le damos formato a los campos
      if( str_word_count( $participant['participant_medical_treatment'] ) > 50 )
        $medical_treatment = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';
      else
        $medical_treatment = $participant['participant_medical_treatment'];

      // -------–-------–-------–-------–-------–-------–-------–
      // Botones
      // -------–-------–-------–-------–-------–-------–-------–
      
      // Botón de calendario
      $schedule_icon = '
        <a href="/participant?pid2=' . $participant['participant_id2'] . '" class="p-button">
          ' . app_get_svg_icon( 'schedule' ) . '
        </a>
      ';

      // Botón de editar
      $edit_icon = '
        <div data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="edit-icon p-button">
          ' . app_get_svg_icon( 'pen' ) . '
        </div>
      ';

      // Botón de borrar
      $delete_icon = '
        <div data-type="user" data-id2="' . $participant['participant_id2'] . '" class="delete-icon p-button">
          ' . app_get_svg_icon( 'trash' ) . '
        </div>
      ';

      // Definimos las celdas
      $cells = [
          'participant_name'              => new TableCell( $participant['participant_name'] )
        , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
        , 'participant_medical_treatment' => new TableCell( $medical_treatment, ['class' => 'max-w-[14rem]'] )
        , 'schedule_icon'                 => new TableCell( $schedule_icon, ['class' => 'text-center w-12 icon-container schedule-icon'] )
        , 'edit_icon'                     => new TableCell( $edit_icon, ['class' => 'text-center w-12 icon-container'] )
        , 'delete_icon'                   => new TableCell( $delete_icon, ['class' => 'text-center w-12 icon-container'] )
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
    $table_html = $table->html();

    // Encapsulamos
    $value = '
      <h2 class="subtitle font-semibold my-4">' . pl_label( 'participants' ) . '</h2>
      ' . $table_html . '
    ';

    return $value;
  }

  /**
   * Genera una fila en formato HTML para un participante específico utilizando `TableRow`.
   * 
   * @param string $participant_id2 Identificador del participante (`participant_id2`).
   * @return string HTML de la fila del participante, o una cadena vacía si no existe.
   */
  public function table_row_participants( string $participant_id2 ): string
  {
    // Capturamos los datos del participante
    $participant = ( new Participants() )->GetRow( $participant_id2 );
    if( empty( $participant ) )
      return '';
    $participant = $participant[0];

    /*
      Array | participant
        [participant_id] => 5
        [user_id] => 11
        [participant_name] => Carlos
        [participant_birth_date] => 2015-06-21
        [participant_allergies] => Ninguna
        [participant_special_needs] => Ninguna
        [participant_medical_treatment] => No aplica
    */

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

    // Botón de editar
    $edit_icon = '
      <div data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="edit-icon p-button">
        ' . app_get_svg_icon( 'pen' ) . '
      </div>
    ';

    // Botón de borrar
    $delete_icon = '
      <div data-type="user" data-id2="' . $participant['participant_id2'] . '" class="delete-icon p-button">
        ' . app_get_svg_icon( 'trash' ) . '
      </div>
    ';

    // Definimos las celdas
    $cells = [
        'participant_name'              => new TableCell( $participant['participant_name'] )
      , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
      , 'participant_medical_treatment' => new TableCell( $medical_treatment, ['class' => 'max-w-[14rem]'] )
      , 'schedule_icon'                 => new TableCell( $schedule_icon, ['class' => 'text-center w-12 icon-container schedule-icon'] )
      , 'edit_icon'                     => new TableCell( $edit_icon, ['class' => 'text-center w-12 icon-container'] )
      , 'delete_icon'                   => new TableCell( $delete_icon, ['class' => 'text-center w-12 icon-container'] )
    ];

    // Creamos la fila con `TableRow`
    $row = new TableRow( $cells, [
        'id'        => 'row-' . $participant['participant_id2']
      , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row'
      , 'data-type' => 'participant_info'
      , 'data-id2'  => $participant['participant_id2']
    ] );

    // Retornamos la fila convertida a HTML
    return $row->html();
  }

  // ------------------------------------------------------------------------------------------------------------------
  // AJAX
  // ------------------------------------------------------------------------------------------------------------------
  
  /**
   * Edita los datos de un tutor y actualiza la vista dinámicamente.
   * 
   * @param array $fields Datos del tutor a actualizar.
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_edit_tutor( array $fields ): array
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
      $required_fields = [
          'user_name'
        , 'user_relationship'
        , 'user_email'
        , 'user_dni'
        , 'user_phone_number'
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

      // --------------------------------------------------------------------------------------------------------------
      // Edit
      // --------------------------------------------------------------------------------------------------------------

      $sql = '
        update ' . DB_PROJECT . '.user_details set
            user_name         = ?
          , user_relationship = ?
          , user_email        = ?
          , user_dni          = ?
          , user_phone_number = ?
        where
          user_id = ? and
          detail_id2 = ?
      ';
      $params = [
          $fields['user_name']
        , $fields['user_relationship']
        , $fields['user_email']
        , $fields['user_dni']
        , $fields['user_phone_number']
        , $_SESSION['app']['user']['user_id']
        , $fields['id2']
      ];
      
      $db->pl_query_prepared( $sql, $params );

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_users( $fields['id2'] );

      // Rellenamos los objetos a actualizar
      $kwargs   = ['elem' => '#row-' . $fields['id2'], 'color' => 'green'];
      $elements = [
          ['selector' => '#row-' . $fields['id2'], 'method_name' => 'update' , 'value'      => $html]
        , ['selector' => '#row-' . $fields['id2'], 'method_name' => 'execute', 'func_name'  => 'highlight_row', 'kwargs' => $kwargs]
      ];

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
   * Edita los datos de un participante y actualiza la base de datos.
   * 
   * @param array $fields Datos del participante a actualizar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_edit_participant( array $fields ): array
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
      $required_fields = [
          'participant_name'
        , 'participant_birth_date'
        , 'participant_allergies'
        , 'participant_special_needs'
        , 'participant_medical_treatment'
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

      // --------------------------------------------------------------------------------------------------------------
      // Edit
      // --------------------------------------------------------------------------------------------------------------

      $sql = '
        update ' . DB_PROJECT . '.participants set
            participant_name              = ?
          , participant_birth_date        = ?
          , participant_allergies         = ?
          , participant_special_needs     = ?
          , participant_medical_treatment = ?
        where
          user_id = ? and
          participant_id2 = ?
      ';
      $params = [
          $fields['participant_name']
        , $fields['participant_birth_date']
        , $fields['participant_allergies']
        , $fields['participant_special_needs']
        , $fields['participant_medical_treatment']
        , $_SESSION['app']['user']['user_id']
        , $fields['id2']
      ];
      
      $db->pl_query_prepared( $sql, $params );

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_participants( $fields['id2'] );

      // Rellenamos los objetos a actualizar
      $kwargs   = ['elem' => '#row-' . $fields['id2'], 'color' => 'green'];
      $elements = [
          ['selector' => '#row-' . $fields['id2'], 'method_name' => 'update' , 'value'      => $html]
        , ['selector' => '#row-' . $fields['id2'], 'method_name' => 'execute', 'func_name'  => 'highlight_row', 'kwargs' => $kwargs]
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
   * Obtiene y muestra un popup con los detalles del usuario seleccionado.
   * 
   * @param array $fields Contiene el identificador del usuario (`id2`).
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_user( array $fields ): array
  {
    $value            = [];
    $db               = new Model();
    $mod_user_details  = new UserDetails();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Buscamos los datos del usuario solicitado
      $user_detail = $mod_user_details->GetRow( $fields['id2'] )[0];
      if( !$user_detail )
        break;
      
      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh]">

            <h3 class="text-2xl mb-4">' . pl_label( 'edit_legal_tutor' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <form data-type="user" data-id2="' . $user_detail['detail_id2'] . '" class="account-form modal_content">
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'legal_tutor_full_name' ) . '</label>
                <input type="text" name="user_name" placeholder="' . pl_label( 'legal_tutor_full_name_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $user_detail['user_name'] . '">
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'legal_tutor_relationship' ) . '</label>
                <input type="text" name="user_relationship" placeholder="' . pl_label( 'legal_tutor_relationship_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $user_detail['user_relationship'] . '">
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'email' ) . '</label>
                <input type="text" name="user_email" placeholder="' . pl_label( 'legal_tutor_email_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $user_detail['user_email'] . '">
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'legal_tutor_dni' ) . '</label>
                <input type="text" name="user_dni" placeholder="' . pl_label( 'legal_tutor_dni_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $user_detail['user_dni'] . '">
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'phone_number' ) . '</label>
                <input type="tel" id="user_phone_number" name="user_phone_number" placeholder="' . pl_label( 'legal_tutor_phone_1' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $user_detail['user_phone_number'] . '">
              </div>

              <div class="flex justify-end">
                <button type="submit" class="custom-submit bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                  ' . pl_label( 'send-button' ) . '
                </button>
              </div>
            </form>

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

    $db->close();
    return $value;
  }

  /**
   * Obtiene y muestra un popup con los detalles del usuario seleccionado.
   * 
   * @param array $fields Contiene el identificador del usuario (`id2`).
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_user_info( array $fields ): array
  {
    $value            = [];
    $db               = new Model();
    $mod_user_details = new UserDetails();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Buscamos los datos del usuario solicitado
      $user_detail = $mod_user_details->GetRow( $fields['id2'] )[0];
      if( !$user_detail )
        break;

      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh]">

            <h3 class="text-2xl mb-4">' . pl_label( 'legal_tutor' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <div class="modal_content">
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'legal_tutor_full_name' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $user_detail['user_name'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'legal_tutor_relationship' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $user_detail['user_relationship'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'email' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $user_detail['user_email'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'legal_tutor_dni' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $user_detail['user_dni'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'phone_number' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $user_detail['user_phone_number'] . '</div>
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

    $db->close();
    return $value;
  }

  /**
   * Obtiene y muestra un popup con los detalles del participante seleccionado.
   * 
   * @param array $fields Contiene el identificador del participante (`id2`).
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_participant( array $fields ): array
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

            <h3 class="text-2xl mb-4">' . pl_label( 'edit_participant' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <form data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="account-form modal_content">
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_full_name' ) . '</label>
                <input type="text" name="participant_name" placeholder="' . pl_label( 'participant_full_name_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $participant['participant_name'] . '">
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_birth_date' ) . '</label>
                <input type="date" name="participant_birth_date" placeholder="' . pl_label( 'participant_birth_date_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $participant['participant_birth_date'] . '">
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_allergies' ) . '</label>
                <textarea name="participant_allergies" placeholder="' . pl_label( 'participant_allergies_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300">' . $participant['participant_allergies'] . '</textarea>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_special_needs' ) . '</label>
                <textarea name="participant_special_needs" placeholder="' . pl_label( 'participant_special_needs_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300">' . $participant['participant_special_needs'] . '</textarea>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_medical_treatment' ) . '</label>
                <textarea name="participant_medical_treatment" placeholder="' . pl_label( 'participant_medical_treatment_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300">' . $participant['participant_medical_treatment'] . '</textarea>
              </div>

              <div class="flex justify-end">
                <button type="submit" class="custom-submit bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                  ' . pl_label( 'send-button' ) . '
                </button>
              </div>
            </form>

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

            <div class="modal_content">
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
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $participant['participant_allergies'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_special_needs' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $participant['participant_special_needs'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_medical_treatment' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $participant['participant_medical_treatment'] . '</div>
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
}

?>