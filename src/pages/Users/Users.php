<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class UsersController
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
  public function table_users( string $where = '' ): string
  {
    global $role;
    
    $value            = '';
    $mod_user_details = new UserDetails();

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    if( $role === 0 )
      $users = $mod_user_details->GetRowsUser( $_SESSION['app']['user']['user_id'], $where );
    else
      $users = $mod_user_details->GetRows( $where );

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
    $table = new Table( ['id' => 'users_table', 'class' => 'p-table', 'role' => 'table'] );
    $table->addColumn( 'user_name'        , new TableColumn( pl_label( 'name' )         , ['scope' => 'col'] ) );
    $table->addColumn( 'user_email'       , new TableColumn( pl_label( 'email' )        , ['scope' => 'col'] ) );
    $table->addColumn( 'user_role'        , new TableColumn( pl_label( 'role' )         , ['scope' => 'col', 'class' => 'hidden md:table-cell'] ) );
    $table->addColumn( 'user_dni'         , new TableColumn( pl_label( 'dni' )          , ['scope' => 'col', 'class' => 'hidden md:table-cell'] ) );
    $table->addColumn( 'user_phone_number', new TableColumn( pl_label( 'phone_number' ) , ['scope' => 'col', 'class' => 'hidden md:table-cell'] ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $users as $user )
    {
      // Definimos las celdas
      $cells = [
          'user_name'         => new TableCell( $user['user_name'] )
        , 'user_email'        => new TableCell( $user['user_email'] )
        , 'user_role'         => new TableCell( $user['user_relationship'], ['class' => 'hidden md:table-cell'] )
        , 'user_dni'          => new TableCell( $user['user_dni']         , ['class' => 'hidden md:table-cell'] )
        , 'user_phone_number' => new TableCell( $user['user_phone_number'], ['class' => 'hidden md:table-cell'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $user['detail_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row'
        , 'data-type' => 'user_info'
        , 'data-id2'  => $user['detail_id2']
        , 'role'      => 'row'
      ] ) );
    }
  
    // --------------------------------------------------------------------------
    // Encapsulamos
    // --------------------------------------------------------------------------

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
      $html = $this->table_users( $fields['query'] );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#users_table', 'method_name'  => 'update' , 'value' => $html]
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

            <h3 class="text-2xl mb-4">' . pl_label( 'user' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <div class="modal_content space-y-5">
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'user_name' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $user_detail['user_name'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'user_relationship' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $user_detail['user_relationship'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'email' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $user_detail['user_email'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'user_dni' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md min-h-[36px]">' . $user_detail['user_dni'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'phone_number' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md min-h-[36px]">' . $user_detail['user_phone_number'] . '</div>
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
}

?>