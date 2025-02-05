<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

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
        0       => $this->form_tutor()
      , 1       => $this->form_monitor()
      , 2       => $this->form_admin()
      , default => ''
    };

    return $value;
  }

  public function form_tutor(): string
  {
    $value = '';
    $db    = new pl_model();

    // --------------------------------------------------------------------------------
    // Tabla usuarios
    // --------------------------------------------------------------------------------

    // Buscamos las cuentas relacionadas al usuario
    $sql = 'select * from ' . DB_PROJECT . '.user_details where user_id = ' . $_SESSION['app']['user']['user_id'];
    $users = $db->pl_query( $sql, true );

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
    $table_users = new Table( ['id' => 'users_table', 'class' => 'table-ui'] );

    // Columnas
    $table_users->addColumn( 'user_name'        , new TableColumn( 'Name'        , ['id' => 'name_col']          ) );
    $table_users->addColumn( 'user_email'       , new TableColumn( 'Email'       , ['id' => 'email_col']         ) );
    $table_users->addColumn( 'user_relationship', new TableColumn( 'Relationship', ['id' => 'relationship_col']  ) );
    $table_users->addColumn( 'user_birth_date'  , new TableColumn( 'Birth date'  , ['id' => 'birth_date_col']    ) );
    $table_users->addColumn( 'user_dni'         , new TableColumn( 'DNI'         , ['id' => 'dni_col']           ) );
    $table_users->addColumn( 'user_phone_number', new TableColumn( 'Phone number', ['id' => 'phone_number_col']  ) );
    $table_users->addColumn( 'edit_icon'        , new TableColumn( ''            , ['id' => 'edit_icon']         ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $users as $user )
    {
      $edit_icon = '
        <div data-type="user" data-id2="' . $user['detail_id2'] . '" class="edit-icon p-2 rounded-lg bg-indigo-600 flex items-center justify-center">
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
      $table_users->addRow( new TableRow( $cells ) );
    }

    // Convertimos la tabla a HTML
    $table_users_html = $table_users->html();

    // --------------------------------------------------------------------------------
    // Tabla participantes
    // --------------------------------------------------------------------------------

    // Buscamos los participantes relacionados al usuario
    $sql = 'select * from ' . DB_PROJECT . '.participants where user_id = ' . $_SESSION['app']['user']['user_id'];
    $participants = $db->pl_query( $sql, true );

    // Inicializamos la tabla y sus columnas
    $table_participants = new Table( ['id' => 'participants_table', 'class' => 'table-ui'] );

    // Columnas
    $table_participants->addColumn( 'participant_name'             , new TableColumn( 'Name'             , ['id' => 'p_name_col']          ) );
    $table_participants->addColumn( 'participant_birth_date'       , new TableColumn( 'Birth Date'       , ['id' => 'p_birth_date_col']    ) );
    $table_participants->addColumn( 'participant_address'          , new TableColumn( 'Address'          , ['id' => 'p_address_col']       ) );
    $table_participants->addColumn( 'participant_medical_treatment', new TableColumn( 'Medical Treatment', ['id' => 'p_medical_treatment'] ) );
    $table_participants->addColumn( 'edit_icon'                    , new TableColumn( ''                 , ['id' => 'edit_icon']           ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $participants as $participant )
    {
      // Le damos formato a los campos
      $participant['participant_medical_treatment'] = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';

      $edit_icon = '
        <div data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="edit-icon p-2 rounded-lg bg-indigo-600 flex items-center justify-center">
          ' . app_get_svg_icon( 'pen' ) . '
        </div>
      ';

      // Definimos las celdas
      $cells = [
          'participant_name'              => new TableCell( $participant['participant_name'] )
        , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
        , 'participant_address'           => new TableCell( $participant['participant_address'] )
        , 'participant_medical_treatment' => new TableCell( $participant['participant_medical_treatment'], ['class' => 'max-w-[14rem]'] )
        , 'edit_icon'                     => new TableCell( $edit_icon, ['class' => 'text-center w-12 edit-icon-container'] )
      ];

      // Añadimos la fila
      $table_participants->addRow( new TableRow( $cells ) );
    }

    // Convertimos la tabla a HTML
    $table_participants_html = $table_participants->html();
  
    // --------------------------------------------------------------------------
    // Encapsulamos
    // --------------------------------------------------------------------------

    $value = '
      <h2 class="subtitle font-semibold m-4">' . pl_label( 'users' ) . '</h2>
      ' . $table_users_html . '
  
      <hr class="my-12">

      <h2 class="subtitle font-semibold m-4">' . pl_label( 'participants' ) . '</h2>
      ' . $table_participants_html . '
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

  public function ajax_popup_user( string $detail_id2 ): string
  {
    $value  = [];
    $db     = new pl_model();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';

    do
    {
      // Buscamos los datos del usuario solicitado
      $sql = '
        select
          *
        from ' . DB_PROJECT . '.user_details
        where
          detail_id2 = "' . $db->esc( $detail_id2 ) .'"
      ';
      


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