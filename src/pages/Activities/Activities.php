<?php

class ActivitiesController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();

    return;
  }

  public function list( $where = '' ): string
  {
    global $images;
  
    $db     = new pl_model();
    $value  = '';
  
    // ------------------------------------------------------------------------------------------------------
    // Activities grid
    // ------------------------------------------------------------------------------------------------------
  
    $sql = 'select * from ' . DB_PROJECT . '.activities' . $where;
    $db->pl_query( $sql );
    while( $db->next_row() )
    {
      $activity = $db->get_row();
      $activity['category_icons'] = '';
      $normalized_activity_name   = pl_normalize( $activity['activity_name'] );
  
      /*
        Array
          [activity_id] => 1
          [activity_id2] => 58b3d1f2bf25ba639db113ccdbd37e08
          [activity_name] => Misión de Exploradores
          [activity_description] => Los niños trabajan juntos como astronautas en una misión espacial, resolviendo desafíos y explorando un nuevo planeta mientras fortalecen su espíritu de equipo.
          [activity_time] => 2025-01-13 15:00:00
      */

      $value .= '
        <div class="group flex w-full flex-col overflow-hidden rounded-3xl shadow-landing relative">

          <img class="w-full h-52 object-cover object-top" src="{{ activity_image }}" alt="activity image" />

          <div class="flex flex-col px-4 py-2 flex-1">
            <div class="flex flex-col flex-1 justify-start">
              <h3 class="my-2 subtitle tracking-tight text-slate-900 hover:underline">' . $activity['activity_name'] . '</h3>
              <hr class="my-2">
              <h4 class="small-title font-bold">Description</h4>
              <p class="body-text mt-2 mb-6">' . $activity['activity_description'] . '</p>
              <p class="body-text mt-2 mb-6">' . $activity['activity_time'] . '</p>
            </div>
          </div>

          <button type="button" class="open_modal_activity absolute top-4 right-4 bg-indigo-600 text-white p-1 rounded-full hover:bg-indigo-500 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m0 0l3-3m-3 3l3 3"/>
            </svg>
          </button>

          <div class="card_modal_activity hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="modal_content relative bg-white p-10 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
              
              <div class="flex items-center justify-between mb-8">
                <h3 class="text-3xl font-semibold text-gray-900">Detalles de la Actividad</h3>
                <button type="button" class="close_modal_activity text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Cerrar">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              <div class="space-y-6 text-gray-800 text-lg">
                <div class="flex justify-between items-center border-b pb-2">
                  <span class="font-medium">ID</span>
                  <span class="font-light">' . $activity['activity_id'] . '</span>
                </div>
                <div class="flex justify-between items-center border-b pb-2">
                  <span class="font-medium">Nombre</span>
                  <span class="font-light">' . $activity['activity_name'] . '</span>
                </div>
                <div class="flex justify-between items-center border-b pb-2">
                  <span class="font-medium">Descripción</span>
                  <span class="font-light">' . $activity['activity_description'] . '</span>
                </div>
                <div class="flex justify-between items-center border-b pb-2">
                  <span class="font-medium">Hora</span>
                  <span class="font-light">' . $activity['activity_time'] . '</span>
                </div>
              </div>

              

            </div>
          </div>

        </div>
      ';
    }
  
    // Encapsulamos
    $value = '
      <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        ' . $value . '
      </div>
    ';
  
    $db->close();
    return $value;
  }
  
  // Función para devolver el número de actividades
  public function count_list( $where = '' ): string
  {
    $db     = new pl_model();
    $value  = '';
  
    // Consultamos el número de actividades
    $sql = 'select * from ' . DB_PROJECT . '.activities' . $where;
    $db->query( $sql );
  
    // Capturamos el número de filas y lo añadimos a un String
    $num_rows   = $db->get_num_rows();
    $result_str = ( $num_rows <> 1 ) ? 'results': 'result';
    $value      = '<h2 class="subtitle font-semibold mb-8 mt-2">Found ' . $num_rows . ' ' . $result_str . ' for <span class="text-[#5560f5]">Activities</span></h2>';
  
    $db->close();
    
    return $value;
  }
  
}

?>