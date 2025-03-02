<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class GroupsController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    return;
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
          <span>' . pl_label( 'add-group' ) . '</span>
        </button>
      '; 
    }

    return $value;
  }

  // --------------------------------------------------------------------------------
  // Tabla grupos
  // --------------------------------------------------------------------------------

  /**
   * Genera la tabla de grupos.
   * 
   * @return string HTML de la tabla de grupos.
   */
  public function table_groups( string $where = '' ): string
  {
    global $role;

    $value            = '';
    $mod_groups       = new Groups();
    $mod_user_details = new UserDetails();

    // Capturamos los grupos
    $groups = $mod_groups->GetRows( $where );

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'groups_table', 'class' => 'p-table'] );

    // Columnas
    $table->addColumn( 'group_name'        , new TableColumn( pl_label( 'name' )                ) );
    $table->addColumn( 'participants_count', new TableColumn( pl_label( 'participants_count' )  ) );
    $table->addColumn( 'monitor_name'      , new TableColumn( pl_label( 'monitor_name' )        ) );

    // Si es un admin, mostramos los iconos de editar y borrar
    if( $role === 2 )
    {
      $table->addColumn( 'edit_icon'  , new TableColumn( '', ['id' => 'edit_icon']    ) );
      $table->addColumn( 'delete_icon', new TableColumn( '', ['id' => 'delete_icon']  ) );
    }

    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $groups as $group )
    {
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
        , 'participants_count' => new TableCell( $group['group_size'] )
        , 'monitor_name'       => new TableCell( $monitor_name )
      ];

      // --------------------------------------------------------------------------------
      // ADMIN
      // --------------------------------------------------------------------------------

      // Si es un admin, mostramos los iconos de editar y borrar
      if( $role === 2 )
      {
        $edit_icon = '
          <div data-type="group" data-gid2="' . $group['group_id2'] . '" class="edit-icon p-button">
            ' . app_get_svg_icon( 'pen', 'black' ) . '
          </div>
        ';

        $delete_icon = '
          <div data-type="group" data-gid2="' . $group['group_id2'] . '" class="delete-icon p-button">
            ' . app_get_svg_icon( 'trash', 'black' ) . '
          </div>
        ';

        $cells['edit_icon']   = new TableCell( $edit_icon   , ['class' => 'text-center w-12 icon-container'] );
        $cells['delete_icon'] = new TableCell( $delete_icon , ['class' => 'text-center w-12 icon-container'] );
      }

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $group['group_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
        , 'data-type' => 'group_info'
        , 'data-id2'  => $group['group_id2']
        , 'data-href' => '/group?gid2=' . $group['group_id2']
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
    return $value;
  }

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
      , 'participants_count' => new TableCell( $group['group_size'] )
      , 'monitor_name'       => new TableCell( $monitor_name )
    ];

    // --------------------------------------------------------------------------------
    // ADMIN
    // --------------------------------------------------------------------------------

    // Si es un admin, mostramos los iconos de editar y borrar
    if( $role === 2 )
    {
      $edit_icon = '
        <div data-type="group" data-gid2="' . $group['group_id2'] . '" class="edit-icon p-button">
          ' . app_get_svg_icon( 'pen', 'black' ) . '
        </div>
      ';

      $delete_icon = '
        <div data-type="group" data-gid2="' . $group['group_id2'] . '" class="delete-icon p-button">
          ' . app_get_svg_icon( 'trash', 'black' ) . '
        </div>
      ';

      $cells['edit_icon']   = new TableCell( $edit_icon, ['class' => 'text-center w-12 icon-container'] );
      $cells['delete_icon'] = new TableCell( $delete_icon, ['class' => 'text-center w-12 icon-container'] );
    }

    // Creamos la fila con `TableRow`
    $row = new TableRow( $cells, [
        'id'        => 'row-' . $group['group_id2']
      , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
      , 'data-href' => '/group?gid2=' . $group['group_id2']
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
      if( !$fields['query'] )
      {
        $message = pl_label( 'required_field' ) . ': query';
        break;
      } 

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
   * Añade un grupo.
   * 
   * @param array<string, string> $fields Datos del grupo a crear.
   * @return array<string, mixed> Respuesta con resultado, mensaje y elementos para actualizar el DOM.
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
      $required_fields = [
          'group_name'
        , 'group_size'
        , 'monitor_select'
      ];

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

      // Verificamos que no exista ningún grupo con el mismo nombre
      $sql = '
        select
          *
        from ' . DB_PROJECT . '.groups
        where
          group_name = ?
      ';
      $result = $db->pl_query_prepared( $sql, [$fields['group_name']], true );
      if( $result )
      {
        // Mostramos una alerta
        $elements = app_generate_alert( true, pl_label( 'group_already_exists' ) );
        break;
      }

      // Buscamos el monitor_id
      $mod_users_details = new UserDetails();
      $monitor = $mod_users_details->GetRow( $fields['monitor_select'] );
      if( !empty( $monitor[0] ) )
        $monitor_id = $monitor[0]['user_id'];
      else
      {
        $elements = app_generate_alert( true, pl_label( 'monitor_not_found' ) );
        break;
      }

      // Generamos un identificador único para el grupo
      $group_id2 = pl_random();
      $sql = '
        insert into ' . DB_PROJECT . '.groups (
            group_id2
          , group_name
          , group_size
          , monitor_id
        ) values ( ?, ?, ?, ? )
      ';
      $params = [
          $group_id2
        , $fields['group_name']
        , $fields['group_size']
        , $monitor_id ?? 0
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
   * Edita los datos de un grupo y actualiza la base de datos.
   * 
   * @param array $fields Datos del grupo a actualizar.
   * @return array Respuesta con resultado, mensaje y elementos para actualizar el DOM.
   */
  public function ajax_edit_group( array $fields ): array
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
      $required_fields = [
          'group_name'
        , 'group_size'
        , 'monitor_select'
      ];

      foreach( $required_fields as $required_field )
      {
        if( !array_key_exists( $required_field, $fields ) )
        {
          $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': ' . $required_field );
          break 2;
        }
      }

      // Buscamos el monitor_id
      $mod_users_details = new UserDetails();
      $monitor = $mod_users_details->GetRow( $fields['monitor_select'] );
      if( !empty( $monitor[0] ) )
        $monitor_id = $monitor[0]['user_id'];
      else
      {
        $elements = app_generate_alert( true, pl_label( 'monitor_not_found' ) );
        break;
      }

      // --------------------------------------------------------------------------------------------------------------
      // Edit
      // --------------------------------------------------------------------------------------------------------------
      $sql = '
        update ' . DB_PROJECT . '.groups set
            group_name  = ?
          , group_size  = ?
          , monitor_id  = ?
        where
            group_id2 = ?
      ';
      $params = [
          $fields['group_name']
        , $fields['group_size']
        , $monitor_id ?? 0
        , $fields['gid2']
      ];

      $db->pl_query_prepared( $sql, $params );

      // --------------------------------------------------------------------------------------------------------------
      // Generamos la alerta de éxito
      // --------------------------------------------------------------------------------------------------------------
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_groups( $fields['gid2'] );

      // Rellenamos los objetos a actualizar
      $kwargs   = ['elem' => '#row-' . $fields['gid2'], 'color' => 'green'];
      $elements = array_merge( $elements, [
          ['selector' => '#row-' . $fields['gid2'], 'method_name' => 'update' , 'value' => $html]
        , ['selector' => '#row-' . $fields['gid2'], 'method_name' => 'execute', 'func_name' => 'highlight_row', 'kwargs' => $kwargs]
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
        delete from ' . DB_PROJECT . '.groups
        where
          group_id = ?
      ';
      $db->pl_query_prepared( $sql, [$group_id] );

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
          u.user_id, ud.*
        from users u
        left join groups g on u.user_id = g.monitor_id
        left join user_details ud on u.user_id = ud.user_id
        where
          u.role = 1 and
          g.monitor_id is null
      ';
      $monitors = $db->pl_query_prepared( $sql, [], true );

      // Generamos el select
      $select = '';
      foreach( $monitors as $monitor_id => $monitor )
      {
        $selected = $monitor_id == 0 ? 'selected' : '';
        $select   .= '<option ' . $selected . ' value="' . $monitor['detail_id2'] . '">' . $monitor['user_name'] . '</option>';
      }

      // Encapsulamos
      $select = '
        <select id="monitor_select" name="monitor_select" class="p-select">
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
              ' . app_custom_input( 'group_name', 'text' ) . '
              ' . $select . '

              <div class="w-full">
                <label for="group_size" class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'group_size' ) . '</label>
                <input type="number" name="group_size" placeholder="' . pl_label( 'group_size_placeholder' ) . '" class="p-input w-full">
              </div>

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
   * Obtiene y muestra un popup con el formulario para editar un grupo.
   * 
   * @param array $fields Contiene el identificador del grupo (`gid2`).
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_edit( array $fields ): array
  {
    $value       = [];
    $mod_groups  = new Groups();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    $db = new Model( DB_PROJECT );

    do
    {
      // --------------------------------------------------------------------------------------------------------------
      // Buscamos los datos del grupo solicitado
      // --------------------------------------------------------------------------------------------------------------
      $group = $mod_groups->GetGroupGID( $fields['gid2'] )[0];
      if( empty( $group ) )
        break;

      // Capturamos los monitores sin grupo asignado
      $sql = '
        select
          u.user_id, ud.*
        from users u
        left join groups g on u.user_id = g.monitor_id
        left join user_details ud on u.user_id = ud.user_id
        where
          u.role = 1 and
          g.monitor_id is null
      ';
      $monitors = $db->pl_query_prepared( $sql, [], true );

      // Generamos el select
      $select = '';
      foreach( $monitors as $monitor_id => $monitor )
      {
        $selected = $monitor_id == 0 ? 'selected' : '';
        $select   .= '<option ' . $selected . ' value="' . $monitor['detail_id2'] . '">' . $monitor['user_name'] . '</option>';
      }

      // Encapsulamos
      $select = '
        <select id="monitor_select" name="monitor_select" class="p-select">
          ' . $select . '
        </select>
      ';

      // --------------------------------------------------------------------------------------------------------------
      // Formulario
      // --------------------------------------------------------------------------------------------------------------
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">

            <h3 class="text-2xl mb-4">' . pl_label( 'edit_group' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <form data-type="edit-group-form" data-gid2="' . $group['group_id2'] . '" class="edit-group-form modal_content space-y-5">

              ' . app_custom_input( 'group_name', 'text', $group['group_name'], 0 ) . '
              ' . $select . '

              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'group_size' ) . '</label>
                <input type="number" name="group_size" placeholder="' . pl_label( 'group_size_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $group['group_size'] . '">
              </div>

              <div class="flex justify-end">
                <button type="submit" class="p-button">
                  <i class="icon">send</i>
                  <span>' . pl_label( 'send-button' ) . '</span>
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

}

?>