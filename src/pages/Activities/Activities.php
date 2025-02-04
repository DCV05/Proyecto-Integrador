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
        <div class="group flex w-full flex-col overflow-hidden rounded-3xl shadow-landing">

          <a class="h-auto overflow-hidden" href="/activity/' . $normalized_activity_name . '">
            <img class="w-full h-52 object-cover object-top" src="{{ activity_image }}" alt="activity image" />
          </a>

          <div class="flex flex-col px-4 py-2 flex-1">

            <div class="flex flex-col flex-1 justify-start">
              <a href="/activity/' . $normalized_activity_name . '" class="my-2 subtitle tracking-tight text-slate-900 hover:underline">' . $activity['activity_name'] . '</a>

              <hr class="my-2">
              <h4 class="small-title font-bold">Description</h4>
              <p class="body-text mt-2 mb-6">' . $activity['activity_description'] . '</p>
              <p class="body-text mt-2 mb-6">' . $activity['activity_time'] . '</p>
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