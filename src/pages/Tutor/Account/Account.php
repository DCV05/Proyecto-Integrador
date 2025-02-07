<?php declare( strict_types = 1 );

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
    $value            = '';
    $mod_user_details = new UserDetails();

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    $users = $mod_user_details->GetRows( $_SESSION['app']['user']['user_id'] );

    /*
      Array | account
        [detail_id] => 4
        [user_id] => 11
        [user_name] => Daniel
        [user_email] => tutor1@example.com
        [user_birth_date] => 2025-02-04
        [user_dni] => 34213213
        [user_phone_number] => 644753740
    */

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'users_table', 'class' => 'table-ui'] );

    // Columnas
    $table->addColumn( 'user_name'        , new TableColumn( 'Name'        , ['id' => 'name_col']          ) );
    $table->addColumn( 'user_email'       , new TableColumn( 'Email'       , ['id' => 'email_col']         ) );
    $table->addColumn( 'user_relationship', new TableColumn( 'Relationship', ['id' => 'relationship_col']  ) );
    $table->addColumn( 'user_birth_date'  , new TableColumn( 'Birth date'  , ['id' => 'birth_date_col']    ) );
    $table->addColumn( 'user_dni'         , new TableColumn( 'DNI'         , ['id' => 'dni_col']           ) );
    $table->addColumn( 'user_phone_number', new TableColumn( 'Phone number', ['id' => 'phone_number_col']  ) );
    $table->addColumn( 'edit_icon'        , new TableColumn( ''            , ['id' => 'edit_icon']         ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $users as $user )
    {
      $edit_icon = '
        <div data-type="user" data-id2="' . $user['detail_id2'] . '" class="edit-icon cursor-pointer p-2 rounded-lg bg-indigo-600 flex items-center justify-center">
          ' . app_get_svg_icon( 'pen' ) . '
        </div>
      ';

      // Definimos las celdas
      $cells = [
          'user_name'         => new TableCell( $user['user_name'] )
        , 'user_email'        => new TableCell( $user['user_email'] )
        , 'user_relationship' => new TableCell( $user['user_relationship'] )
        , 'user_birth_date'   => new TableCell( $user['user_birth_date'] )
        , 'user_dni'          => new TableCell( $user['user_dni'] )
        , 'user_phone_number' => new TableCell( $user['user_phone_number'] )
        , 'edit_icon'         => new TableCell( $edit_icon, ['class' => 'text-center w-10 edit-icon-container'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, ['id' => 'row-' . $user['detail_id2']] ) );
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
    $mod_user_details = new UserDetails();

    // Capturamos los datos del usuario
    $user = $mod_user_details->GetRow( $detail_id2 );
    if( empty( $user ) )
      return null;

    /*
      Array | user
        [detail_id] => 4
        [user_id] => 11
        [user_name] => Daniel
        [user_email] => tutor1@example.com
        [user_birth_date] => 2025-02-04
        [user_dni] => 34213213
        [user_phone_number] => 644753740
    */

    $edit_icon = '
      <div data-type="user" data-id2="' . $detail_id2 . '" class="edit-icon cursor-pointer p-2 rounded-lg bg-indigo-600 flex items-center justify-center">
        ' . app_get_svg_icon( 'pen' ) . '
      </div>
    ';

    // Definimos las celdas
    $cells = [
        'user_name'         => new TableCell( $user['user_name'] )
      , 'user_email'        => new TableCell( $user['user_email'] )
      , 'user_relationship' => new TableCell( $user['user_relationship'] )
      , 'user_birth_date'   => new TableCell( $user['user_birth_date'] )
      , 'user_dni'          => new TableCell( $user['user_dni'] )
      , 'user_phone_number' => new TableCell( $user['user_phone_number'] )
      , 'edit_icon'         => new TableCell( $edit_icon, ['class' => 'text-center w-10 edit-icon-container'] )
    ];

    // Retornamos la fila de tabla generada
    $table_row = new TableRow( $cells, ['id' => 'row-' . $detail_id2] );
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
    $value            = '';
    $mod_participants = new Participants();

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    $participants = $mod_participants->GetRows( $_SESSION['app']['user']['user_id'] );

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'participants_table', 'class' => 'table-ui'] );

    // Columnas
    $table->addColumn( 'participant_name'             , new TableColumn( 'Name'             , ['id' => 'p_name_col']          ) );
    $table->addColumn( 'participant_birth_date'       , new TableColumn( 'Birth Date'       , ['id' => 'p_birth_date_col']    ) );
    $table->addColumn( 'participant_medical_treatment', new TableColumn( 'Medical Treatment', ['id' => 'p_medical_treatment'] ) );
    $table->addColumn( 'edit_icon'                    , new TableColumn( ''                 , ['id' => 'edit_icon']           ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $participants as $participant )
    {
      // Le damos formato a los campos
      $participant['participant_medical_treatment'] = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';
      $edit_icon = '
        <div data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="edit-icon cursor-pointer p-2 rounded-lg bg-indigo-600 flex items-center justify-center">
          ' . app_get_svg_icon( 'pen' ) . '
        </div>
      ';

      // Definimos las celdas
      $cells = [
          'participant_name'              => new TableCell( $participant['participant_name'] )
        , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
        , 'participant_medical_treatment' => new TableCell( $participant['participant_medical_treatment'], ['class' => 'max-w-[14rem]'] )
        , 'edit_icon'                     => new TableCell( $edit_icon, ['class' => 'text-center w-12 edit-icon-container'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'      => 'row-' . $participant['participant_id2']
        , 'class'   => 'hover:bg-gray-100 cursor-pointer table-row-link'
        , 'data-href' => '/tutor/participant?pid2=' . $participant['participant_id2']
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
    $mod_participants = new Participants();

    // Capturamos los datos del participante
    $participant = $mod_participants->GetRow( $participant_id2 );
    if( empty( $participant ) )
      return '';

    $participant = $participant[0];

    /*
      Array | participant
        [participant_id] => 5
        [user_id] => 11
        [participant_name] => Carlos
        [participant_birth_date] => 2015-06-21
        [participant_address] => Calle Falsa 123
        [participant_allergies] => Ninguna
        [participant_special_needs] => Ninguna
        [participant_medical_treatment] => No aplica
    */

    // Le damos formato a los campos
    $participant['participant_medical_treatment'] = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';
    $edit_icon = '
      <div data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="edit-icon cursor-pointer p-2 rounded-lg bg-indigo-600 flex items-center justify-center">
        ' . app_get_svg_icon( 'pen' ) . '
      </div>
    ';

    // Definimos las celdas
    $cells = [
        'participant_name'              => new TableCell( $participant['participant_name'] )
      , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
      , 'participant_medical_treatment' => new TableCell( $participant['participant_medical_treatment'], ['class' => 'max-w-[14rem]'] )
      , 'edit_icon'                     => new TableCell( $edit_icon, ['class' => 'text-center w-12 edit-icon-container'] )
    ];

    // Creamos la fila con `TableRow`
    $row = new TableRow( $cells, [
        'id'        => 'row-' . $participant['participant_id2']
      , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
      , 'data-href' => '/tutor/participant?pid2=' . $participant['participant_id2']
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
    $db     = new pl_model();

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
        , 'user_birth_date'
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
            user_name         = "' . $db->esc( $fields['user_name'] )         . '"
          , user_relationship = "' . $db->esc( $fields['user_relationship'] ) . '"
          , user_email        = "' . $db->esc( $fields['user_email'] )        . '"
          , user_dni          = "' . $db->esc( $fields['user_dni'] )          . '"
          , user_birth_date   = "' . $db->esc( $fields['user_birth_date'] )   . '"
          , user_phone_number = "' . $db->esc( $fields['user_phone_number'] ) . '"
        where
          user_id = ' . $_SESSION['app']['user']['user_id'] . ' and
          detail_id2 = "' . $db->esc( $fields['id2'] ) . '"
      ';
      $db->pl_query( $sql );

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
    $db     = new pl_model();

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
        , 'participant_address'
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
            participant_name              = "' . $db->esc( $fields['participant_name'] )              . '"
          , participant_birth_date        = "' . $db->esc( $fields['participant_birth_date'] )        . '"
          , participant_address           = "' . $db->esc( $fields['participant_address'] )           . '"
          , participant_allergies         = "' . $db->esc( $fields['participant_allergies'] )         . '"
          , participant_special_needs     = "' . $db->esc( $fields['participant_special_needs'] )     . '"
          , participant_medical_treatment = "' . $db->esc( $fields['participant_medical_treatment'] ) . '"
        where
          user_id = ' . $_SESSION['app']['user']['user_id'] . ' and
          participant_id2 = "' . $db->esc( $fields['id2'] ) . '"
      ';
      $db->pl_query( $sql );

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
    $value  = [];
    $db     = new pl_model();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Buscamos los datos del usuario solicitado
      $sql = '
        select
          *
        from ' . DB_PROJECT . '.user_details
        where
          detail_id2 = "' . $db->esc( $fields['id2'] ) . '"
      ';
      $db->pl_query( $sql );
      if( !$db->next_row() )
        break;

      // Capturamos el registro
      $user_detail = $db->get_row();

      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-scroll">

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
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'legal_tutor_birth_date' ) . '</label>
                <input type="date" name="user_birth_date" placeholder="' . pl_label( 'legal_tutor_birth_date_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $user_detail['user_birth_date'] . '">
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
          [participant_address] => Calle Falsa 123
          [participant_allergies] => Ninguna
          [participant_special_needs] => Ninguna
          [participant_medical_treatment] => No aplica
      */

      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-scroll">

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
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_address' ) . '</label>
                <input type="text" name="participant_address" placeholder="' . pl_label( 'participant_address_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $participant['participant_address'] . '">
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
}

?>