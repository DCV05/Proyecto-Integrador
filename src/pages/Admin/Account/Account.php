<?php

class AdminAccountController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    return;
  }

  // --------------------------------------------------------------------------------
  // Detalles de la cueanta
  // --------------------------------------------------------------------------------

  /**
   * Genera el perfil de la cuenta del usuario.
   * 
   * @return string HTML con los detalles de la cuenta y la foto de perfil.
   */
  public function account_profile(): string
  {
    // Datos del usuario desde la sesión
    $user_email = $_SESSION['app']['user']['user_email'];

    $user_photo = !empty( $files )
      ? '<img src="' . str_replace( $_SESSION['polaris']['document_root'], $_SESSION['polaris']['complex_domain'], reset( $files ) ) . '" class="w-10 h-10 mr-2 rounded-full shadow-landing border-2 border-gray-300">'
      : '<div class="flex items-center justify-center w-24 h-24 bg-blue-500 text-white font-bold rounded-full">' . ucfirst( $user_email[0] ) . '</div>';

    // Contenedor principal
    $value = '
      <div class="bg-white shadow-lg rounded-lg p-6 mb-6 flex flex-col items-center text-center">
        
        <div class="relative">
          ' . $user_photo . '
          <input type="file" id="upload_profile_photo" class="hidden" accept="image/*">
          <label for="upload_profile_photo" class="absolute bottom-0 right-0 bg-blue-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600">
            <i class="fa-solid fa-camera"></i>
          </label>
        </div>

        <h2 class="text-2xl font-semibold text-gray-900 mt-4">' . $user_email . '</h2>

        <button id="btn-change-password" class="mt-4 bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600">
          ' . pl_label( 'change_password_button' ) . '
        </button>

      </div>
    ';

    return $value;
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