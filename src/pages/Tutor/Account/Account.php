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
    app_restrict();

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
  public function table_tutors( string $where = '' ): string
  {
    $value            = '';
    $mod_user_details = new UserDetails();

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    $users = $mod_user_details->GetRowsUser( $_SESSION['app']['user']['user_id'], $where );

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
        <div data-type="user" data-id2="' . $user['detail_id2'] . '" class="delete-icon p-button">
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
    $value = $table->html();
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
        [user_email] => user1@example.com
        [user_dni] => 34213213
        [user_phone_number] => 644753740
    */

    $edit_icon = '
      <div data-type="user" data-id2="' . $detail_id2 . '" class="edit-icon p-button">
        ' . app_get_svg_icon( 'pen' ) . '
      </div>
    ';

    $delete_icon = '
      <div data-type="user" data-id2="' . $user['detail_id2'] . '" class="delete-icon p-button">
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
  public function table_participants( string $where = '' ): string
  {
    $value = '';

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    $participants = ( new Participants() )->GetRows( $_SESSION['app']['user']['user_id'], $where );

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
        <div data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="delete-icon p-button">
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
    $value = $table->html();
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
      <div data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="delete-icon p-button">
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
  
  public function ajax_form_search_participants( array $fields ): array
  {
    $value = [];

    // Inicializamos las variables de la llamada AJAX
    $result   = 0;
    $message  = '';
    $redirect = '';
    $elements = [];

    do
    {
      // Recargamos el HTML de la tabla de participantes
      $html = $this->table_participants( $fields['query'] );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#participants_table', 'method_name' => 'update', 'value' => $html]
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
  
  public function ajax_form_search_users( array $fields ): array
  {
    $value = [];

    // Inicializamos las variables de la llamada AJAX
    $result   = 0;
    $message  = '';
    $redirect = '';
    $elements = [];

    do
    {
      // Recargamos el HTML de la tabla de usuarios
      $html = $this->table_users( $fields['query'] );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#users_table', 'method_name' => 'update', 'value' => $html]
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
   * Crea un usuario
   * 
   * @param array $fields Datos del user a actualizar.
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_add_user( array $fields ): array
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
          // Mostramos una alerta
          $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': ' . $required_field );
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // Insert
      // --------------------------------------------------------------------------------------------------------------

      $detail_id2 = pl_random();
      $sql = '
        insert into ' . DB_PROJECT . '.user_details (
            detail_id2
          , user_id
          , user_name
          , user_relationship
          , user_email
          , user_dni
          , user_phone_number
        ) values ( ?, ?, ?, ?, ?, ?, ? )
      ';
      
      $params = [
          $detail_id2
        , $_SESSION['app']['user']['user_id']
        , $fields['user_name']
        , $fields['user_relationship']
        , $fields['user_email']
        , $fields['user_dni']
        , $fields['user_phone_number']
      ];
      
      $db->pl_query_prepared( $sql, $params );

      // Capturamos el ID del nuevo user
      $detail_id = $db->get_last_id();

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_users( $detail_id2 );

      // Rellenamos los objetos a actualizar
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );

      $kwargs   = ['elem' => '#row-' . $detail_id2, 'color' => 'green'];
      $elements = array_merge( $elements, [
          ['selector' => '#users_table tbody tr:last', 'method_name'  => 'insertBefore', 'value' => $html]
        , ['selector' => '#row-' . $detail_id2, 'method_name' => 'execute', 'func_name' => 'highlight_row', 'kwargs' => $kwargs]
      ] );

      // Si llega hasta aquí, está todo OK
      $result = 1;

      // ------------------------------------------------------------------------------
      // Subida de foto de perfil
      // ------------------------------------------------------------------------------

      if( empty( $fields['user_image_upload'] ) || $fields['user_image_upload']['size'] > 0 )
        break;

      // Generamos el nombre del directorio de destino. Si no existe lo creamos
      $dir = ASSETS_PATH . '/panel/users';
      if( !is_dir( $dir ) && !@mkdir( $dir ) )
      {
        $message = pl_label( 'error-create-dir' );
        break;
      }
      
      // Calculamos la ruta de la imagen y el nombre final
      $source     = $fields['user_image_upload']['name'];
      $extension  = pathinfo( $source, PATHINFO_EXTENSION );
      $target     = $dir . '/' . pl_number_id( $detail_id ) . '_' . $detail_id2 . '.' . $extension;
      
      // Movemos el fichero temporal al directorio final
      if( !move_uploaded_file( $fields['user_image_upload']['tmp_name'], $target ) )
      {
        $message = pl_label( 'error-upload' );
        break;
      }

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
   * Edita los datos de un user y actualiza la vista dinámicamente.
   * 
   * @param array $fields Datos del user a actualizar.
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_edit_user( array $fields ): array
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
          // Mostramos una alerta
          $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': ' . $required_field );
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

      // Cambiamos el correo si es la cuenta principal
      if( $_SESSION['app']['user']['is_main'] == 1 )
      {
        // Modificamos el correo del usuario
        $sql = '
          update ' . DB_PROJECT . '.users set
            user_email = ?
          where
            user_id = ?
        ';
        $params = [
            $fields['user_email']
          , $_SESSION['app']['user']['user_id']
        ];
        $db->pl_query_prepared( $sql, $params );
      }

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_users( $fields['id2'] );

      // Rellenamos los objetos a actualizar
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );
      $kwargs   = ['elem' => '#row-' . $fields['id2'], 'color' => 'green'];
      $elements = array_merge( $elements, [
          ['selector' => '#row-' . $fields['id2'], 'method_name' => 'update' , 'value'      => $html]
        , ['selector' => '#row-' . $fields['id2'], 'method_name' => 'execute', 'func_name'  => 'highlight_row', 'kwargs' => $kwargs]
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
   * Borra los datos de un usuario
   * 
   * @param array $fields Datos del usuario a eliminar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_delete_user( array $fields ): array
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
      $required_fields = ['id2'];
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

      // Buscamos si la usuario existe
      $user_detail = ( new UserDetails() )->GetRow( $fields['id2'] )[0];
      if( $user_detail )
        $detail_id = $user_detail['detail_id'];
      else
      {
        $message = pl_label( 'user_detail_not_found' );
        break;
      }

      // Borramos las vinculaciones con monitores
      $sql = '
        delete from ' . DB_PROJECT . '.user_details
        where
          detail_id = ?
      ';
      $db->pl_query_prepared( $sql, [$detail_id] );

      // Rellenamos los objetos a actualizar
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );
      $elements = array_merge( $elements, [
        ['selector' => '#row-' . $fields['id2'], 'method_name' => 'remove']
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
      $required_fields = [
          'participant_name'
        , 'participant_birth_date'
      ];

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

      // Insertamos los datos del nuevo participante
      $participant_id2 = pl_random();
      $sql = '
        insert into ' . DB_PROJECT . '.participants (
            participant_id2
          , user_id
          , participant_name
          , participant_birth_date
          , participant_allergies
          , participant_special_needs
          , participant_medical_treatment
        ) values ( ?, ?, ?, ?, ?, ?, ? )
      ';

      $params = [
          $participant_id2
        , $_SESSION['app']['user']['user_id']
        , $fields['participant_name']
        , $fields['participant_birth_date']
        , $fields['participant_allergies']          ?? ''
        , $fields['participant_special_needs']      ?? ''
        , $fields['participant_medical_treatment']  ?? ''
      ];

      $db->pl_query_prepared( $sql, $params );

      // Capturamos el ID del nuevo user
      $participant_id = $db->get_last_id();

      // ------------------------------------------------------------------------------
      // Subida de foto de perfil
      // ------------------------------------------------------------------------------

      if( !empty( $fields['participant_image_upload'] ) && $fields['participant_image_upload']['size'] > 0 )
      {
        // Generamos el nombre del directorio de destino. Si no existe lo creamos
        $dir = ASSETS_PATH . '/panel/participants';
        if( !is_dir( $dir ) && !@mkdir( $dir ) )
        {
          $message = pl_label( 'error-create-dir' );
          continue;
        }

        // Calculamos la ruta de la imagen y el nombre final
        $source     = $fields['participant_image_upload']['name'];
        $extension  = pathinfo( $source, PATHINFO_EXTENSION );
        $target     = $dir . '/' . pl_number_id( $participant_id ) . '_' . $participant_id2 . '.' . $extension;
        
        // Movemos el fichero temporal al directorio final
        if( !move_uploaded_file( $fields['participant_image_upload']['tmp_name'], $target ) )
        {
          $message = pl_label( 'error-upload' );
          continue;
        }
      }

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_participants( $participant_id2 );

      // Rellenamos los objetos a actualizar
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );

      $kwargs   = ['elem' => '#row-' . $participant_id2, 'color' => 'green'];
      $elements = array_merge( $elements, [
          ['selector' => '#participants_table tbody tr:last', 'method_name'  => 'insertBefore', 'value' => $html]
        , ['selector' => '#row-' . $participant_id2, 'method_name' => 'execute', 'func_name' => 'highlight_row', 'kwargs' => $kwargs]
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
          // Mostramos una alerta
          $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': ' . $required_field );
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
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );
      $kwargs   = ['elem' => '#row-' . $fields['id2'], 'color' => 'green'];
      $elements = array_merge( $elements, [
          ['selector' => '#row-' . $fields['id2'], 'method_name' => 'update' , 'value'      => $html]
        , ['selector' => '#row-' . $fields['id2'], 'method_name' => 'execute', 'func_name'  => 'highlight_row', 'kwargs' => $kwargs]
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
   * Borra los datos de una actividad
   * 
   * @param array $fields Datos de la actividad a eliminar.
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
      $required_fields = ['id2'];
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
      $participant = ( new Participants() )->GetRow( $fields['id2'] )[0];
      if( $participant )
        $participant_id = $participant['participant_id'];
      else
      {
        $message = pl_label( 'participant_not_found' );
        break;
      }

      // Borramos las vinculaciones con monitores
      $sql = '
        delete from ' . DB_PROJECT . '.participants
        where
          participant_id = ?
      ';
      $db->pl_query_prepared( $sql, [$participant_id] );

      // Rellenamos los objetos a actualizar
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );
      $elements = array_merge( $elements, [
        ['selector' => '#row-' . $fields['id2'], 'method_name' => 'remove']
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
      
      // --------------------------------------------------------------------------------------------------------------
      // Construcción del contenido del modal
      // --------------------------------------------------------------------------------------------------------------
      $content = '
        <form data-type="user" data-id2="' . $user_detail['detail_id2'] . '" class="tutor-form modal_content space-y-5">
          ' . app_custom_input( 'user_name'         , 'text', $user_detail['user_name'] )         . '
          ' . app_custom_input( 'user_relationship' , 'text', $user_detail['user_relationship'] ) . '
          ' . app_custom_input( 'user_email'        , 'text', $user_detail['user_email'] )        . '
          ' . app_custom_input( 'user_dni'          , 'text', $user_detail['user_dni'] )          . '
          ' . app_custom_input( 'user_phone_number' , 'tel', $user_detail['user_phone_number'] )  . '

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

      // --------------------------------------------------------------------------------------------------------------
      // Generamos el modal con `app_generate_modal`
      // --------------------------------------------------------------------------------------------------------------
      $elements = app_generate_modal( pl_label( 'edit_user' ), $content );

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

      // --------------------------------------------------------------------------------------------------------------
      // Construcción del contenido del modal
      // --------------------------------------------------------------------------------------------------------------
      $content = '
        <div class="modal_content space-y-5">
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'user_full_name' ) . '</label>
            <div class="p-input">' . $user_detail['user_name'] . '</div>
          </div>
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'user_relationship' ) . '</label>
            <div class="p-input">' . $user_detail['user_relationship'] . '</div>
          </div>
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'email' ) . '</label>
            <div class="p-input">' . $user_detail['user_email'] . '</div>
          </div>
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'user_dni' ) . '</label>
            <div class="p-input">' . $user_detail['user_dni'] . '</div>
          </div>
          <div>
            <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'phone_number' ) . '</label>
            <div class="p-input">' . $user_detail['user_phone_number'] . '</div>
          </div>
        </div>
      ';

      // --------------------------------------------------------------------------------------------------------------
      // Generamos el modal con `app_generate_modal`
      // --------------------------------------------------------------------------------------------------------------
      $elements = app_generate_modal( pl_label( 'add_tutor' ), $content );

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

        // --------------------------------------------------------------------------------------------------------------
        // Construcción del contenido del modal
        // --------------------------------------------------------------------------------------------------------------
        $content = '
          <form data-type="participant" data-id2="' . $participant['participant_id2'] . '" class="tutor-form modal_content space-y-5">
            ' . app_custom_input( 'participant_name'                , 'text', $participant['participant_name'] )        . '
            ' . app_custom_input( 'participant_birth_date'          , 'date', $participant['participant_birth_date'] )  . '
            ' . app_custom_textarea( 'participant_allergies'        , $participant['participant_allergies'] )           . '
            ' . app_custom_textarea( 'participant_special_needs'    , $participant['participant_special_needs'] )       . '
            ' . app_custom_textarea( 'participant_medical_treatment', $participant['participant_medical_treatment'] )   . '

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
   * Formulario de creación de un nuevo usuario
   * 
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_add_user(): array
  {
    $value = [];

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      $form_content = '
        <form data-type="add_user" class="add-tutor-form modal_content space-y-5">
          ' . app_custom_input( 'user_name', 'text' )         . '
          ' . app_custom_input( 'user_email', 'email' )       . '
          ' . app_custom_input( 'user_relationship', 'text' ) . '
          ' . app_custom_input( 'user_dni', 'text' )          . '
          ' . app_custom_input( 'user_phone_number', 'text' ) . '

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
      
      $elements = app_generate_modal( pl_label( 'add_tutor' ), $form_content );

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
   * Formulario de creación de un nuevo participante
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

    do
    {
      $form_content = '
        <form data-type="add_participant" class="add-participant-form modal_content space-y-5">
          ' . app_custom_input( 'participant_name', 'text' )     . '
          ' . app_custom_input( 'participant_birth_date', 'date' )    . '
          ' . app_custom_textarea( 'participant_allergies' )          . '
          ' . app_custom_textarea( 'participant_medical_treatment' )  . '
      
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