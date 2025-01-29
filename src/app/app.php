<?php

// -------------------------------------------------------------------------------------
// Funciones globales de la aplicación
// -------------------------------------------------------------------------------------

// Función para devolver las cabeceras (link y script) de la aplicación
function app_headers(): string
{
  // Cabeceras HTML del proyecto
  $headers = '
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/script.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://kit.fontawesome.com/870c4283ef.js" crossorigin="anonymous"></script>
  ';

  return $headers;
}

// Sidebar para paciente
function app_patient_sidebar(): string
{
  $current_url = $_SESSION['polaris']['url_relative'];
  
  // Inicializamos el HTML del aside
  $links_html = '';
  $entries    = [
      ['title' => 'Inicio'       , 'link' => '/patient?uid2=' . pl_get( 'uid2' ), 'icon' => '🏠']
    , ['title' => 'Cerrar sesión', 'link' => '/login'                           , 'icon' => '🚪']
  ];

  // Formato para el checked
  foreach( $entries as $entry )
  {
    // Estilo para cada link
    $checked = $entry['link'] === $current_url ? 'bg-gray-300 active': '';

    // Le insertamos formato al link
    $links_html .= sprintf(
        '<a href="%s" class="w-full text-left px-2 py-2 rounded-lg hover:bg-gray-300 %s">%s %s</a>'
      , $entry['link']
      , $checked
      , $entry['icon']
      , $entry['title']
    );
  }

  // Encapsulamos el sidebar
  $value = '
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-200 shadow-md" id="sidebar">
      <div class="p-4">
        <h1 class="text-xl font-bold">Ambulatorio</h1>
      </div>
      <nav id="links-container" class="flex flex-col p-4 space-y-2">
        ' . $links_html . '
      </nav>
    </aside>
  ';

  return $value;
}


// Sidebar para doctor
function app_doctor_sidebar(): string
{
  $current_url = $_SESSION['polaris']['url_relative'];
  
  // Inicializamos el HTML del aside
  $links_html = '';
  $entries    = [
      ['title' => 'Inicio'        , 'link' => '/doctor?uid2=' . pl_get( 'uid2' ), 'icon' => '🏠']
    , ['title' => 'Cerrar sesión' , 'link' => '/login'                          , 'icon' => '🚪']
  ];

  // Formato para el checked
  foreach( $entries as $entry )
  {
    // Estilo para cada link
    $checked = $entry['link'] === $current_url ? 'bg-gray-300 active': '';

    // Le insertamos formato al link
    $links_html .= sprintf(
        '<a href="%s" class="w-full text-left px-2 py-2 rounded-lg hover:bg-gray-300 %s">%s %s</a>'
      , $entry['link']
      , $checked
      , $entry['icon']
      , $entry['title']
    );
  }

  // Encapsulamos el sidebar
  $value = '
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-200 shadow-md" id="sidebar">
      <div class="p-4">
        <h1 class="text-xl font-bold">Ambulatorio</h1>
      </div>
      <nav id="links-container" class="flex flex-col p-4 space-y-2">
        ' . $links_html . '
      </nav>
    </aside>
  ';

  return $value;
}

// Función para pasar de un formato 2024-08-05 a Aug 5, 24
function app_convert_date_format( $date ): string
{
  $date_object = DateTime::createFromFormat( 'Y-m-d', $date );

  if ( $date_object === false )
    return "Invalid date";

  return $date_object->format( 'M d, Y' );
}

?>