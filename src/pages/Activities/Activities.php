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
      : $this->grid_activities()
    ;

    return $value;
  }

  public function layout_buttons(): string
  {
    $value = '';

    // Si no existe el layout, lo designamos grid por defecto
    if( !isset( $_SESSION['layout_mode'] ) || $_SESSION['layout_mode'] === 'grid' )
    {
      $grid_checked = 'bg-[#5560f5] text-white';
      $list_checked = 'bg-gray-200 hover:bg-[#5560f5] hover:text-white';
    }
    else
    {
      $grid_checked = 'bg-gray-200 hover:bg-[#5560f5] hover:text-white';
      $list_checked = 'bg-[#5560f5] text-white';
    }

    // HTML radio buttons
    $value = '
      <div id="layout_buttons" class="flex gap-2">
        <button 
            id="button_grid" 
            class="py-2 px-3 ' . $grid_checked . ' transform transition duration-300 rounded-lg" 
            type="button">
          <i class="fa-solid fa-grid-2"></i>
        </button>

        <button 
            id="button_table" 
            class="py-2 px-3 ' . $list_checked . ' transform transition duration-300 rounded-lg" 
            type="button">
          <i class="fa-solid fa-list-ul"></i>
        </button>
      </div>
    ';
  
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
  public function table_activities(): string
  {
    $value         = '';
    $mod_activities = new Activities();

    // Capturamos todas las actividades
    $activities = $mod_activities->GetRows();

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'activities_table', 'class' => 'table-ui'] );

    // Columnas
    $table->addColumn( 'activity_name'        , new TableColumn( 'Name'        , ['id' => 'a_name_col']          ) );
    $table->addColumn( 'activity_description' , new TableColumn( 'Description' , ['id' => 'a_description_col']   ) );
    $table->addColumn( 'activity_time'        , new TableColumn( 'Date & Time' , ['id' => 'a_time_col']          ) );
    $table->addColumn( 'edit_icon'            , new TableColumn( ''            , ['id' => 'edit_icon']           ) );

    // Iteramos cada actividad y la añadimos a la tabla
    foreach( $activities as $activity )
    {
      // Le damos formato a los campos
      $activity['activity_description'] = substr( $activity['activity_description'], 0, 50 ) . '...';

      $edit_icon = '
        <div data-type="activity" data-id2="' . $activity['activity_id2'] . '" class="edit-icon cursor-pointer p-2 rounded-lg bg-indigo-600 flex items-center justify-center">
          ' . app_get_svg_icon( 'pen' ) . '
        </div>
      ';

      // Definimos las celdas
      $cells = [
          'activity_name'        => new TableCell( $activity['activity_name'] )
        , 'activity_description' => new TableCell( $activity['activity_description'], ['class' => 'max-w-[14rem]'] )
        , 'activity_time'        => new TableCell( $activity['activity_time'] )
        , 'edit_icon'            => new TableCell( $edit_icon, ['class' => 'text-center w-12 edit-icon-container'] )
      ];

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

    // Le damos formato a los campos
    $activity['activity_description'] = substr( $activity['activity_description'], 0, 50 ) . '...';

    $edit_icon = '
      <div data-type="activity" data-id2="' . $activity['activity_id2'] . '" class="edit-icon cursor-pointer p-2 rounded-lg bg-indigo-600 flex items-center justify-center">
        ' . app_get_svg_icon( 'pen' ) . '
      </div>
    ';

    // Definimos las celdas
    $cells = [
        'activity_name'        => new TableCell( $activity['activity_name'] )
      , 'activity_description' => new TableCell( $activity['activity_description'], ['class' => 'max-w-[14rem]'] )
      , 'activity_time'        => new TableCell( $activity['activity_time'] )
      , 'edit_icon'            => new TableCell( $edit_icon, ['class' => 'text-center w-12 edit-icon-container'] )
    ];

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
  public function grid_activities(): string
  {
    $value          = '';
    $mod_activities = new Activities();

    // Capturamos todas las actividades
    $activities = $mod_activities->GetRows();

    // Inicializamos el contenedor del grid
    $grid_html = '<div id="activities_table" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';

    // Iteramos cada actividad y la añadimos al grid
    foreach( $activities as $activity )
    {
      $short_description = substr( $activity['activity_description'], 0, 80 ) . '...';

      $grid_html .= '
        <div class="group relative bg-white shadow-lg rounded-2xl overflow-hidden transition transform hover:scale-105 hover:shadow-2xl">
          <a href="/activity?aid2=' . $activity['activity_id2'] . '" class="block w-full h-full">
            <div class="p-6 space-y-4">
              <h3 class="text-xl font-semibold text-gray-900 group-hover:text-indigo-600 transition">' . $activity['activity_name'] . '</h3>
              <p class="text-sm text-gray-500">' . $short_description . '</p>
              <div class="text-sm font-medium text-indigo-600">' . date( 'F j, Y - H:i', strtotime( $activity['activity_time'] ) ) . '</div>
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
            <div class="text-sm font-medium text-indigo-600">' . date( 'F j, Y - H:i', strtotime( $activity['activity_time'] ) ) . '</div>
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
    $db = new pl_model();
  
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
      $html = $this->layout_buttons();
      $elements[] = ['selector' => '#layout_buttons', 'method_name' => 'update' , 'value' => $html];
  
      // Según el layout_mode, mostramos el grid o la tabla
      $html = $_SESSION['layout_mode'] == 'table'
        ? $this->table_activities()
        : $this->grid_activities()
      ;
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

  /**
   * Edita los datos de una actividad y actualiza la base de datos.
   * 
   * @param array $fields Datos de la actividad a actualizar.
   * @return array Respuesta con resultado, mensaje y posible redirección.
   */
  public function ajax_edit_activity( array $fields ): array
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
          'activity_name'
        , 'activity_description'
        , 'activity_time'
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
            activity_name        = "' . $db->esc( $fields['activity_name'] )        . '"
          , activity_description = "' . $db->esc( $fields['activity_description'] ) . '"
          , activity_time        = "' . $db->esc( $fields['activity_time'] )        . '"
        where
          activity_id2 = "' . $db->esc( $fields['id2'] ) . '"
      ';
      $db->pl_query( $sql );

      // Recargamos el HTML de la fila actualizada
      $html = $this->table_row_activities( $fields['id2'] );

      // Rellenamos los objetos a actualizar
      $kwargs   = ['elem' => '#row-' . $fields['id2'], 'color' => 'green'];
      $elements = [
          ['selector' => '#row-' . $fields['id2'], 'method_name' => 'update' , 'value'     => $html]
        , ['selector' => '#row-' . $fields['id2'], 'method_name' => 'execute', 'func_name' => 'highlight_row', 'kwargs' => $kwargs]
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
      $activity = $mod_activity->GetRow( $fields['id2'] );
      if( empty( $activity ) )
        break;

      /*
        Array | activity
          [activity_name] => Excursión al bosque
          [activity_description] => Caminata de exploración con los niños.
          [activity_time] => 2025-06-15 10:00:00
      */

      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-scroll">

            <h3 class="text-2xl mb-4">' . pl_label( 'edit-activity' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <form data-type="activity" data-id2="' . $activity['activity_id2'] . '" class="account-form modal_content">
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'activity_name' ) . '</label>
                <input type="text" name="activity_name" placeholder="' . pl_label( 'activity_name_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . $activity['activity_name'] . '">
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'activity_description' ) . '</label>
                <textarea name="activity_description" placeholder="' . pl_label( 'activity_description_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300">' . $activity['activity_description'] . '</textarea>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'activity_time' ) . '</label>
                <input type="datetime-local" name="activity_time" placeholder="' . pl_label( 'activity_time_placeholder' ) . '" class="custom-input mt-1 transform transition duration-300" value="' . date('Y-m-d\TH:i', strtotime($activity['activity_time'])) . '">
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