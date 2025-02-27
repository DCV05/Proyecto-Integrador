<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class ActivitiesController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    return;
  }

  public function content(): string
  {
    $value = isset( $_SESSION['layout_mode'] ) && $_SESSION['layout_mode'] == 'table'
      ? $this->table_activities()
      : $this->grid_activities();

    return $value;
  }

  public function button_add_activity(): string
  {
    global $role;
    $value = '';

    if( $role === 2 )
    {
      $value = '
        <button type="button" id="btn-add-activity" class="p-button">
          <i class="icon">add</i>
          <span>' . pl_label( 'add-activity' ) . '</span>
        </button>
      '; 
    }

    return $value;
  }

  // --------------------------------------------------------------------------------
  // Tabla actividades
  // --------------------------------------------------------------------------------

  /**
   * Genera la tabla de actividades disponibles.
   * 
   * @return string HTML de la tabla de actividades.
   */
  public function table_activities( string $where = '' ): string
  {
    global $role;

    $value          = '';
    $mod_activities = new Activities();

    // Si el usuario es un monitor, mostramos únicamente sus actividades vinculadas
    $activities = $role != 1
      ? $mod_activities->GetRows( $where )
      : $mod_activities->GetMonitorLinkedRows( $_SESSION['app']['user']['user_id'] );

    // --------------------------------------------------------------------------------
    // Definicón de la tabla
    // --------------------------------------------------------------------------------
    
    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'activities_table', 'class' => 'p-table'] );
    $table->addColumn( 'activity_name'       , new TableColumn( pl_label( 'activity_name' ), ['id' => 'name_col']               ) );
    $table->addColumn( 'activity_description', new TableColumn( pl_label( 'activity_description' ), ['id' => 'description_col'] ) );
    $table->addColumn( 'activity_time'       , new TableColumn( pl_label( 'activity_time' ), ['id' => 'time_col']               ) );

    // Si es un admin, mostramos los iconos de editar y borrar
    if( $role === 2 )
    {
      $table->addColumn( 'edit_icon'  , new TableColumn( '', ['id' => 'edit_icon']    ) );
      $table->addColumn( 'delete_icon', new TableColumn( '', ['id' => 'delete_icon']  ) );
    }

    // --------------------------------------------------------------------------------
    // Formateo de datos y relleno de datos de la tabla
    // --------------------------------------------------------------------------------
    // Iteramos cada actividad y la añadimos a la tabla
    foreach( $activities as $activity )
    {
      // Le damos formato a los campos
      $activity['activity_description_' . DEF_LANG] = substr( $activity['activity_description_' . DEF_LANG], 0, 100 ) . '...';

      $activity['activity_time'] = '
        <div class="flex gap-3">
          <span class="p-tag-blue">
            ' . date( 'd/m/y | H:i', strtotime( $activity['activity_datetime_start'] ) )  . ' - '
              . date( 'H:i', strtotime( $activity['activity_datetime_end'] ) )    . '
          </span>
        </div>
      ';

      // Definimos las celdas
      $cells = [
          'activity_name'        => new TableCell( $activity['activity_name_' . DEF_LANG] )
        , 'activity_description' => new TableCell( $activity['activity_description_' . DEF_LANG], ['class' => 'max-w-[14rem]'] )
        , 'activity_time'        => new TableCell( $activity['activity_time'] )
      ];

      // --------------------------------------------------------------------------------
      // ADMIN
      // --------------------------------------------------------------------------------

      // Si es un admin, mostramos los iconos de editar y borrar
      if( $role === 2 )
      {
        $edit_icon = '
          <div data-type="activity" data-aid2="' . $activity['activity_id2'] . '" class="edit-icon p-button">
            ' . app_get_svg_icon( 'pen', 'black' ) . '
          </div>
        ';

        $delete_icon = '
          <div data-type="activity" data-aid2="' . $activity['activity_id2'] . '" class="delete-icon p-button">
            ' . app_get_svg_icon( 'trash', 'black' ) . '
          </div>
        ';

        $cells['edit_icon']   = new TableCell( $edit_icon   , ['class' => 'text-center w-12 icon-container'] );
        $cells['delete_icon'] = new TableCell( $delete_icon , ['class' => 'text-center w-12 icon-container'] );
      }

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $activity['activity_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
        , 'data-href' => '/activity?aid2=' . $activity['activity_id2']
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
    return $value;
  }
  
  /**
   * Genera una fila en formato HTML para una actividad específica utilizando `TableRow`.
   * 
   * @param string $activity_id2 Identificador de la actividad (`activity_id2`).
   * @return string HTML de la fila de la actividad, o una cadena vacía si no existe.
   */
  public function table_row_activities( string $activity_id2 ): string
  {
    global $role;
    $mod_activities = new Activities();

    // Capturamos los datos de la actividad
    $activity = $mod_activities->GetRow( $activity_id2 )[0];
    if( empty( $activity ) )
      return '';

    /*
      Array | activity
        [activity_id] => 10
        [activity_id2] => abc123
        [activity_name] => Excursión al bosque
        [activity_description] => Caminata de exploración con los niños.
        [activity_time] => 2025-06-15 10:00:00
    */

    // Le damos formato a los campos
    $activity['activity_description_' . DEF_LANG] = substr( $activity['activity_description_' . DEF_LANG], 0, 100 ) . '...';

    $activity['activity_time'] = '
      <div class="flex gap-3">
        <span class="p-tag-blue">
          ' . date( 'd/m/y | H:i', strtotime( $activity['activity_datetime_start'] ) )  . ' - '
            . date( 'H:i', strtotime( $activity['activity_datetime_end'] ) )    . '
        </span>
      </div>
    ';

    // Definimos las celdas
    $cells = [
        'activity_name'        => new TableCell( $activity['activity_name_' . DEF_LANG] )
      , 'activity_description' => new TableCell( $activity['activity_description_' . DEF_LANG], ['class' => 'max-w-[14rem]'] )
      , 'activity_time'        => new TableCell( $activity['activity_time'] )
    ];

    // --------------------------------------------------------------------------------
    // ADMIN
    // --------------------------------------------------------------------------------

    // Si es un admin, mostramos los iconos de editar y borrar
    if( $role === 2 )
    {
      $edit_icon = '
        <div data-type="activity" data-aid2="' . $activity['activity_id2'] . '" class="edit-icon p-button">
          ' . app_get_svg_icon( 'pen', 'black' ) . '
        </div>
      ';

      $delete_icon = '
        <div data-type="activity" data-aid2="' . $activity['activity_id2'] . '" class="delete-icon p-button">
          ' . app_get_svg_icon( 'trash', 'black' ) . '
        </div>
      ';

      $cells['edit_icon']   = new TableCell( $edit_icon   , ['class' => 'text-center w-12 icon-container'] );
      $cells['delete_icon'] = new TableCell( $delete_icon , ['class' => 'text-center w-12 icon-container'] );
    }

    // Creamos la fila con `TableRow`
    $row = new TableRow( $cells, [
        'id'        => 'row-' . $activity['activity_id2']
      , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
      , 'data-href' => '/activity?aid2=' . $activity['activity_id2']
    ] );

    // Retornamos la fila convertida a HTML
    return $row->html();
  }

  /**
   * Genera un grid de actividades en formato de tarjetas.
   * 
   * @return string HTML del grid de actividades.
   */
  public function grid_activities( string $where = '' ): string
  {
    $value          = '';
    $mod_activities = new Activities();

    // Capturamos todas las actividades
    $activities = $mod_activities->GetRows( $where );

    // Inicializamos el contenedor del grid
    $grid_html = '<div id="activities_table" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';

    // Iteramos cada actividad y la añadimos al grid
    foreach( $activities as $activity )
    {
      $short_description = substr( $activity['activity_description_' . DEF_LANG], 0, 80 ) . '...';
      $grid_html .= '
        <div class="group relative bg-white shadow-lg rounded-2xl overflow-hidden transition transform hover:scale-105 hover:shadow-2xl">
          <a href="/activity?aid2=' . $activity['activity_id2'] . '" class="block w-full h-full">
            <div class="p-6 space-y-4">
              <h3 class="text-xl font-semibold text-gray-900 group-hover:text-indigo-600 transition">' . $activity['activity_name_' . DEF_LANG] . '</h3>
              <p class="text-sm text-gray-500">' . $short_description . '</p>

              <span class="p-tag-blue">
                ' . date( 'd/m/y | H:i', strtotime( $activity['activity_datetime_start'] ) )  . ' - '
                  . date( 'H:i', strtotime( $activity['activity_datetime_end'] ) )    . '
              </span>
            </div>
          </a>
        </div>
      ';
    }

    // Cerramos el grid
    $grid_html .= '</div>';

    // Encapsulamos
    $value = $grid_html;
    return $value;
  }

  /**
   * Genera una tarjeta en formato HTML para una actividad específica.
   * 
   * @param string $activity_id2 Identificador de la actividad (`activity_id2`).
   * @return string HTML de la tarjeta de la actividad, o una cadena vacía si no existe.
   */
  public function grid_row_activities( string $activity_id2 ): string
  {
    $mod_activities = new Activities();

    // Capturamos los datos de la actividad
    $activity = $mod_activities->GetRow( $activity_id2 );
    if( empty( $activity ) )
      return '';

    /*
      Array | activity
        [activity_id] => 10
        [activity_id2] => abc123
        [activity_name] => Excursión al bosque
        [activity_description] => Caminata de exploración con los niños.
        [activity_time] => 2025-06-15 10:00:00
    */

    // Le damos formato a la descripción
    $short_description = substr( $activity['activity_description'], 0, 80 ) . '...';

    // Generamos la tarjeta HTML
    $card_html = '
      <div id="card-' . $activity['activity_id2'] . '" class="group relative bg-white shadow-lg rounded-2xl overflow-hidden transition transform hover:scale-105 hover:shadow-2xl">
        <a href="/activity?aid2=' . $activity['activity_id2'] . '" class="block w-full h-full">
          <div class="p-6 space-y-4">
            <h3 class="text-xl font-semibold text-gray-900 group-hover:text-indigo-600 transition">' . $activity['activity_name'] . '</h3>
            <p class="text-sm text-gray-500">' . $short_description . '</p>
            <span class="p-tag-blue">
              ' . date( 'd/m/y | H:i', strtotime( $activity['activity_datetime_start'] ) )  . ' - '
                . date( 'H:i', strtotime( $activity['activity_datetime_end'] ) )    . '
            </span>
          </div>
        </a>
      </div>
    ';

    return $card_html;
  }

  // --------------------------------------------------------------------------------
  // AJAX
  // --------------------------------------------------------------------------------
  
  public function ajax_update_layout( array $fields ): array
  {
    $db = new Model();
  
    // Inicializamos los valores
    $result   = 0;
    $message  = '';
    $redirect = '';
    $elements = []; // Objetos HTML a actualizar en la página
  
    do
    {
      // Cambiamos el valor del modo del layout
      $_SESSION['layout_mode'] = $fields['layout'];

      // Rellenamos los objetos a actualizar
      $html = app_layout_buttons();
      $elements[] = ['selector' => '#layout_buttons', 'method_name' => 'update' , 'value' => $html];
  
      // Según el layout_mode, mostramos el grid o la tabla
      $html = $_SESSION['layout_mode'] == 'table'
        ? $this->table_activities()
        : $this->grid_activities();
      $elements[] = ['selector' => '#activities_table', 'method_name' => 'update', 'value' => $html];
  
      // Si llega aquí está todo OK
      $result = 1;
      break;
  
    } while( false );
  
    $db->close();
  
    $value = [
        'result'    => $result
      , 'message'   => $message
      , 'redirect'  => $redirect
      , 'elements'  => $elements
    ];
  
    return $value;
  }

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
      $html = isset( $_SESSION['layout_mode'] ) && $_SESSION['layout_mode'] == 'table'
      ? $this->table_activities( $fields['query'] )
      : $this->grid_activities( $fields['query'] );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#activities_table', 'method_name'  => 'update' , 'value' => $html]
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
   * Añade una actividad.
   * 
   * @param array $fields Datos de la actividad a crear.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_add_activity( array $fields ): array
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
          'activity_name_es'
        , 'activity_description_es'
        , 'activity_name_en'
        , 'activity_description_en'
        , 'activity_datetime_start'
        , 'activity_datetime_end'
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
      // Insert
      // --------------------------------------------------------------------------------------------------------------

      // Añadimos la nueva actividad
      $activity_id2 = pl_random();
      $sql = '
        insert into ' . DB_PROJECT . '.activities (
            activity_id2
          , activity_name_es
          , activity_description_es
          , activity_name_en
          , activity_description_en
          , activity_datetime_start
          , activity_datetime_end
          , activity_tags_es
          , activity_tags_en
        ) values ( ?, ?, ?, ?, ?, ?, ?, ?, ? )
      ';
      $params = [
          $activity_id2
        , $fields['activity_name_es']
        , $fields['activity_description_es']
        , $fields['activity_name_en']
        , $fields['activity_description_en']
        , $fields['activity_datetime_start']
        , $fields['activity_datetime_end']
        , ''
        , ''
      ];
      $db->pl_query_prepared( $sql, $params );

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_activities( $activity_id2 );

      // Rellenamos los objetos a actualizar
      $kwargs   = ['elem' => '#row-' . $activity_id2, 'color' => 'green'];
      $elements = [
          ['selector' => '#activities_table tbody tr:last', 'method_name'  => 'insertBefore' , 'value' => $html]
        , ['selector' => '#row-' . $activity_id2, 'method_name' => 'execute', 'func_name' => 'highlight_row', 'kwargs' => $kwargs]
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
   * Edita los datos de una actividad y actualiza la base de datos.
   * 
   * @param array $fields Datos de la actividad a actualizar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_edit_activity( array $fields ): array
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
          'activity_name_es'
        , 'activity_description_es'
        , 'activity_name_en'
        , 'activity_description_en'
        , 'activity_datetime_start'
        , 'activity_datetime_end'
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
        update ' . DB_PROJECT . '.activities set
            activity_name_es        = ?
          , activity_description_es = ?
          , activity_name_en        = ?
          , activity_description_en = ?
          , activity_datetime_start = ?
          , activity_datetime_end   = ?
        where
          activity_id2 = ?
      ';
      $params = [
          $fields['activity_name_es']
        , $fields['activity_description_es']
        , $fields['activity_name_en']
        , $fields['activity_description_en']
        , $fields['activity_datetime_start']
        , $fields['activity_datetime_end']
        , $fields['aid2']
      ];
      
      $db->pl_query_prepared( $sql, $params );

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_activities( $fields['aid2'] );

      // Rellenamos los objetos a actualizar
      $kwargs   = ['elem' => '#row-' . $fields['aid2'], 'color' => 'green'];
      $elements = [
          ['selector' => '#row-' . $fields['aid2'], 'method_name' => 'update' , 'value'     => $html]
        , ['selector' => '#row-' . $fields['aid2'], 'method_name' => 'execute', 'func_name' => 'highlight_row', 'kwargs' => $kwargs]
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
   * Borra los datos de una actividad
   * 
   * @param array $fields Datos de la actividad a eliminar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_delete_activity( array $fields ): array
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
      $required_fields = ['aid2'];
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
      // Delete
      // --------------------------------------------------------------------------------------------------------------

      // Buscamos si la actividad existe
      $activity = ( new Activities() )->GetRow( $fields['aid2'] )[0];
      if( $activity )
        $activity_id = $activity['activity_id'];
      else
      {
        $message = pl_label( 'activity_not_found' );
        break;
      }

      // Borramos las vinculaciones con monitores
      $sql = '
        delete from ' . DB_PROJECT . '.activities_monitors
        where
          activity_id = ?
      ';
      $db->pl_query_prepared( $sql, [$activity_id] );

      // Borramos la actividad
      $sql = '
        delete from ' . DB_PROJECT . '.activities
        where
          activity_id = ?
      ';
      $db->pl_query_prepared( $sql, [$activity_id] );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#row-' . $fields['aid2'], 'method_name' => 'remove']
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
   * Obtiene y muestra un popup con los detalles de la actividad seleccionada.
   * 
   * @param array $fields Contiene el identificador de la actividad (`id2`).
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_activity( array $fields ): array
  {
    $value         = [];
    $mod_activity  = new Activities();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Buscamos los datos de la actividad solicitada
      $activity = $mod_activity->GetRow( $fields['aid2'] )[0];
      if( empty( $activity ) )
        break;

      /*
        Array | activity
          [activity_name] => Excursión al bosque
          [activity_description] => Caminata de exploración con los niños.
          [activity_datetime_start] => 2025-06-15 10:00:00
          [activity_datetime_end] => 2025-06-15 12:00:00
      */

      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">

            <h3 class="text-2xl mb-4">' . pl_label( 'edit-activity' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <form data-type="activity" data-aid2="' . $activity['activity_id2'] . '" class="account-form modal_content space-y-5">

              ' . app_custom_input( 'activity_name_es', 'text', $activity['activity_name_es'], 0 ) . '
              ' . app_custom_textarea( 'activity_description_es', $activity['activity_description_es'] ) . '

              ' . app_custom_input( 'activity_name_en', 'text', $activity['activity_name_en'], 0 ) . '
              ' . app_custom_textarea( 'activity_description_en', $activity['activity_description_en'] ) . '

              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'activity_datetime_start' ) . '</label>
                <input type="datetime-local" name="activity_datetime_start" placeholder="' . pl_label( 'activity_datetime_start_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . date('Y-m-d\TH:i', strtotime( $activity['activity_datetime_start'] ) ) . '">
              </div>

              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'activity_datetime_end' ) . '</label>
                <input type="datetime-local" name="activity_datetime_end" placeholder="' . pl_label( 'activity_datetime_end_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . date('Y-m-d\TH:i', strtotime( $activity['activity_datetime_end'] ) ) . '">
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
   * Obtiene y muestra un popup con los detalles de la actividad seleccionada.
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

    do
    {
      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">

            <h3 class="text-2xl mb-4">' . pl_label( 'add_activity' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <form data-type="activity-add" class="activity-form modal_content space-y-5">
              ' . app_custom_input( 'activity_name_es', 'text' ) . '
              ' . app_custom_textarea( 'activity_description_es' ) . '

              ' . app_custom_input( 'activity_name_en', 'text' ) . '
              ' . app_custom_textarea( 'activity_description_en' ) . '

              <div class="w-full">
                <label for="activity_datetime_start" class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'activity_datetime_start' ) . '</label>
                <input type="datetime-local" name="activity_datetime_start" placeholder="' . pl_label( 'activity_datetime_start_placeholder' ) . '" class="p-input w-full">
              </div>

              <div class="w-full">
                <label for="activity_datetime_end" class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'activity_datetime_end' ) . '</label>
                <input type="datetime-local" name="activity_datetime_end" placeholder="' . pl_label( 'activity_datetime_end_placeholder' ) . '" class="p-input w-full">
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