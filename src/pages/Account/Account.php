<?php

class AccountController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();

    return;
  }

  public function account_details(): string
  {
    $value = '';

    // Dependiendo del tipo de usuario mostramos un formulario u otro
    $value = match( ( int ) $_SESSION['app']['user']['role'] )
    {
        0       => $this->tutor_form()
      , 1       => $this->monitor_form()
      , 2       => $this->admin_form()
      , default => ''
    };

    return $value;
  }

  public function tutor_form(): string
  {
    $value = '';
    $db    = new pl_model();

    // Buscamos las cuentas relacionadas al usuario
    $sql = 'select * from ' . DB_PROJECT . '.user_details where user_id = ' . $_SESSION['app']['user']['user_id'];
    $db->pl_query( $sql );

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
    
    $accounts_value = '';
    while( $db->next_row() )
    {
      $account = $db->get_row();
  
      $accounts_value .= '
        <div class="cursor-pointer card relative bg-white rounded-3xl shadow-landing p-6">
          <img class="w-full h-52 object-cover object-top" src="{{ template_image }}" alt="Tutor image" />
          <div class="card_body flex flex-col px-4 py-2 flex-1">
            <div class="flex flex-col flex-1 justify-start">
              <p class="my-2 subtitle tracking-tight text-slate-900">
                ' . $account['user_name'] . '
              </p>
            </div>
          </div>
  
          <div class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-scroll">
  
              <h3 class="text-2xl mb-4">' . pl_label( 'edit-tutor' ) . '</h3>
  
              <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
  
              <form data-type="user" data-id2="' . $account['detail_id2'] . '" class="account-form modal_content">
                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'tutor_full_name' ) . '</label>
                  <input type="text" name="user_name" placeholder="' . pl_label( 'tutor_full_name_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $account['user_name'] . '">
                </div>
                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'tutor_relationship' ) . '</label>
                  <input type="text" name="user_relationship" placeholder="' . pl_label( 'tutor_relationship_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $account['user_relationship'] . '">
                </div>
                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'email' ) . '</label>
                  <input type="text" name="user_email" placeholder="' . pl_label( 'tutor_email_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $account['user_email'] . '">
                </div>
                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'tutor_dni' ) . '</label>
                  <input type="text" name="user_dni" placeholder="' . pl_label( 'tutor_dni_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $account['user_dni'] . '">
                </div>
                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'phone_number' ) . '</label>
                  <input type="tel" id="user_phone_number" name="user_phone_number" placeholder="' . pl_label( 'tutor_phone_1' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $account['user_phone_number'] . '">
                </div>
                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'tutor_birth_date' ) . '</label>
                  <input type="date" name="user_birth_date" placeholder="' . pl_label( 'tutor_birth_date_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $account['user_birth_date'] . '">
                </div>
  
                <div class="flex justify-end">
                  <button type="submit" class="custom-submit bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                    ' . pl_label( 'send-button' ) . '
                  </button>
                </div>
              </form>
  
            </div>
          </div>
        </div>
      ';
    }

    // Buscamos los participantes relacionados al usuario
    $sql = 'select * from ' . DB_PROJECT . '.participants where user_id = ' . $_SESSION['app']['user']['user_id'];
    $db->pl_query( $sql );

    $participants_value = '';
    while( $db->next_row() )
    {
      $participant = $db->get_row();

      /*
        Array | participant
          [participant_id] => 1
          [participant_id2] => e26b20124473be7d8ff9eb4cead70a9f
          [user_id] => 11
          [participant_name] => Diego Sánchez
          [birth_date] => 2016-03-10
          [address] => 
          [allergies] => Alérgico a los frutos secos y al polen. Para evitar cualquier reacción, su dieta en el campamento es estrictamente controlada y su equipo siempre lleva un EpiPen en caso de emergencia. Durante las actividades al aire libre, los monitores supervisan de cerca cualquier exposición al entorno natural, asegurándose de que siempre tenga un espacio seguro donde jugar sin riesgos. Además, su grupo ha sido informado sobre su alergia para fomentar un ambiente de apoyo y cuidado.
          [special_needs] => Para que su experiencia en el campamento sea cómoda, cuenta con un horario estructurado y zonas de descanso donde puede relajarse si necesita un momento de calma. Su monitor ha sido capacitado para comprender sus necesidades y adaptar las actividades según su ritmo, asegurando que pueda participar en cada aventura de manera divertida y sin estrés.
          [medical_treatment] => 
      */
  
      $participants_value .= '
        <div class="cursor-pointer card relative bg-white rounded-3xl shadow-landing p-6">
          <img class="w-full h-52 object-cover object-top" src="{{ template_image }}" alt="Participant image" />
          <div class="card_body flex flex-col px-4 py-2 flex-1">
            <div class="flex flex-col flex-1 justify-start">
              <p class="my-2 subtitle tracking-tight text-slate-900">
                ' . $participant['participant_name'] . '
              </p>
            </div>
          </div>
  
          <div class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-scroll">
  
              <h3 class="text-2xl mb-4">' . pl_label( 'edit-participant' ) . '</h3>
  
              <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
  
              <form data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="participant-form modal_content">

                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_name' ) . '</label>
                  <input
                    type="text"
                    id="participant_name"
                    name="participant_name"
                    placeholder="' . pl_label( 'participant_name_placeholder' ) . '"
                    class="custom-input mt-1 transform transition duration-300"
                    value="' . $participant['participant_name'] . '"
                  >
                </div>

                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_birth_date' ) . '</label>
                  <input
                    type="date"
                    id="participant_birth_date"
                    name="participant_birth_date"
                    placeholder="' . pl_label( 'participant_birth_date_placeholder' ) . '"
                    class="custom-input mt-1 transform transition duration-300"
                    value="' . $participant['participant_birth_date'] . '"
                  >
                </div>

                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_address' ) . '</label>
                  <input
                    type="text"
                    id="participant_address"
                    name="participant_address"
                    placeholder="' . pl_label( 'participant_address_placeholder' ) . '"
                    class="custom-input mt-1 transform transition duration-300"
                    value="' . $participant['participant_address'] . '"
                  >
                </div>

                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_allergies' ) . '</label>
                  <textarea id="participant_allergies" name="participant_allergies" placeholder="' . pl_label( 'participant_allergies_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300">' . $participant['participant_allergies'] . '</textarea>
                </div>

                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_special_needs' ) . '</label>
                  <textarea id="participant_special_needs" name="participant_special_needs" placeholder="' . pl_label( 'participant_special_needs_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300">' . $participant['participant_special_needs'] . '</textarea>
                </div>

                <div>
                  <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_medical_treatment' ) . '</label>
                  <textarea id="participant_medical_treatment" name="participant_medical_treatment" placeholder="' . pl_label( 'participant_medical_treatment_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300">' . $participant['participant_medical_treatment'] . '</textarea>
                </div>

  
                <div class="flex justify-end">
                  <button type="submit" class="custom-submit bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                    ' . pl_label( 'send-button' ) . '
                  </button>
                </div>
              </form>
  
            </div>
          </div>
        </div>
      ';
    }
  
    // --------------------------------------------------------------------------
    // Encapsulamos
    // --------------------------------------------------------------------------
    $value = '
      <div>
        <h2 class="text-2xl font-bold mb-4">' . pl_label( 'tutors_section' ) . '</h2>
        <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-5 gap-8">' 
          . $accounts_value . 
        '</div>
      </div>
  
      <div class="mt-12">
        <h2 class="text-2xl font-bold mb-4">' . pl_label( 'participants_section' ) . '</h2>
        <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-5 gap-8">'
          . $participants_value . 
        '</div>
      </div>
    ';  

    $db->close();
    return $value;
  }
  
  public function ajax_edit_tutor( array $fields ): array
  {
    $value  = [];
    $db     = new pl_model();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';

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

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'    => $result
      , 'message'   => $message
      , 'redirect'  => $redirect
    ];

    $db->close();
    return $value;
  }

  public function ajax_edit_participant( array $fields ): array
  {
    $value  = [];
    $db     = new pl_model();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';

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

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'   => $result
      , 'message'  => $message
      , 'redirect' => $redirect
    ];

    $db->close();
    return $value;
  }
}

?>