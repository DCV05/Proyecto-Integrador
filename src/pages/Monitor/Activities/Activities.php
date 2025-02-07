<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class MonitorActivitiesController
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
    $mod_activities_monitors  = new ActivitiesMonitors();
    $value                    = '';

    // Capturamos todas las actividades relacionadas con un monitor
    $activities = $mod_activities_monitors->GetMonitorRows( $_SESSION['app']['user']['user_id'] );

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'activities_table', 'class' => 'table-ui'] );

    // Columnas
    $table->addColumn( 'activity_name'        , new TableColumn( 'Name'        , ['id' => 'a_name_col']          ) );
    $table->addColumn( 'activity_description' , new TableColumn( 'Description' , ['id' => 'a_description_col']   ) );
    $table->addColumn( 'activity_time'        , new TableColumn( 'Date & Time' , ['id' => 'a_time_col']          ) );

    // Iteramos cada actividad y la añadimos a la tabla
    foreach( $activities as $activity )
    {
      // Le damos formato a los campos
      $activity['activity_description'] = substr( $activity['activity_description'], 0, 50 ) . '...';

      // Definimos las celdas
      $cells = [
          'activity_name'        => new TableCell( $activity['activity_name'] )
        , 'activity_description' => new TableCell( $activity['activity_description'], ['class' => 'max-w-[14rem]'] )
        , 'activity_time'        => new TableCell( $activity['activity_time'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $activity['activity_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
        , 'data-href' => '/monitor/attendance?aid2=' . $activity['activity_id2']
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
    return $value;
  }

  /**
   * Genera un grid de actividades en formato de tarjetas.
   * 
   * @return string HTML del grid de actividades.
   */
  public function grid_activities(): string
  {
    $mod_activities_monitors  = new ActivitiesMonitors();
    $value                    = '';

    // Capturamos todas las actividades relacionadas con un monitor
    $activities = $mod_activities_monitors->GetMonitorRows( $_SESSION['app']['user']['user_id'] );

    // Inicializamos el contenedor del grid
    $grid_html = '<div id="activities_table" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';

    // Iteramos cada actividad y la añadimos al grid
    foreach( $activities as $activity )
    {
      $short_description = substr( $activity['activity_description'], 0, 80 ) . '...';

      $grid_html .= '
        <div class="group relative bg-white shadow-lg rounded-2xl overflow-hidden transition transform hover:scale-105 hover:shadow-2xl">
          <a href="/monitor/attendance?aid2=' . $activity['activity_id2'] . '" class="block w-full h-full">
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
}

?>