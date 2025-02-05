<?php

// -------------------------------------------------------------------------------------
// Contantes globales de la aplicación
// -------------------------------------------------------------------------------------

global $entries;
$entries = [
  [
    'title'  => 'Desktop',
    'link'   => '/desktop',
    'icon'   => app_get_svg_icon( 'desktop' )
  ],
  [
    'title'  => 'Activities',
    'link'   => '/activities',
    'icon'   => app_get_svg_icon( 'activities' )
  ],
  [
    'title'  => 'Account',
    'link'   => '/account',
    'icon'   => app_get_svg_icon( 'account' )
  ]
];

global $heading_entries;
$heading_entries = [
  [
    'title'  => 'Desktop',
    'link'   => '/desktop',
    'icon'   => app_get_svg_icon( 'desktop' )
  ],
  [
    'title'  => 'Activities',
    'link'   => '/activities',
    'icon'   => app_get_svg_icon( 'activities' )
  ],
  [
    'title'  => 'Account',
    'link'   => '/account',
    'icon'   => app_get_svg_icon( 'account' )
  ],
  [
    'title'  => 'Schedule',
    'link'   => '/schedule',
    'icon'   => app_get_svg_icon( 'schedule' )
  ]
];
  
// Colores de los svg
global $colors;
$colors = ['blue', 'green', 'orange'];

// URL actual
global $current_url;

// Quitamos los parámetros GET y el último '/'
$current_url = $_SESSION['polaris']['url_relative'];
$current_url = parse_url( $current_url, PHP_URL_PATH );
if( strlen( $current_url ) > 1 )
  $current_url = rtrim( $current_url, '/' );

// -------------------------------------------------------------------------------------
// Funciones globales de la aplicación
// -------------------------------------------------------------------------------------

// Control de seguridad
function app_security(): void
{
  if( empty( $_SESSION['app']['user'] ) )
    pl_redirect( '/login' );
}

// Función para devolver las cabeceras (link y script) de la aplicación
function app_headers(): string
{
  // Cabeceras HTML del proyecto
  $headers = '
    <link rel="stylesheet" href="/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://kit.fontawesome.com/870c4283ef.js" crossorigin="anonymous"></script>
    <script src="/js/app.js"></script>
    <script src="/js/script.js"></script>
  ';

  return $headers;
}

function app_panel_render_tree(): string
{
  global $entries, $current_url, $colors;
  $value = '';

  // Iteramos cada ruta
  foreach( $entries as $entry_index => $entry )
  {
    // ------------------------------------------------------------------------------------------------
    // Estilos del item
    // ------------------------------------------------------------------------------------------------

    // Asignamos unos colores según el índice
    $link   = $entry['link'] ?? '';
    $color  = $colors[$entry_index] ?? 'blue';

    // Si el item pertenece a la URL actual, añadimos una clase bold
    $bold = $current_url == $link ? 'font-bold' : '';
    $icon = !empty( $entry['icon'] ) ? '<div class="w-9 h-9 bg-' . $color . '-500 shadow-landing white-svg text-i-2xl tree-icon-container">' . $entry['icon'] . '</div>' : '';

    // Encapsulamos
    $value .= sprintf(
      '<div class="grid grid-cols-[auto_1fr] gap-2 px-[1.125rem] py-2 items-start">
        %s
        <a href="%s" class="text-base %s mt-1">%s</a>
      </div>',
      $icon,
      $link,
      $bold,
      $entry['title']
    );
  }

  return $value;
}

function app_panel_heading(): string
{
  global $heading_entries, $current_url, $colors;
  global $current_url;
  
  $value = '';

  // Headings
  foreach( $heading_entries as $entry_index => $entry )
  {
    // Si no es la URL actual, continuamos
    if( $current_url !== $entry['link'] )
      continue;

    // Estilos del heading
    $color = $colors[$entry_index] ?? 'blue';

    $value = '
      <div class="flex flex-row gap-2 items-center flex-shrink-0">
        <div class="w-11 h-11 bg-' . $color . '-500 shadow-landing white-svg tree-icon-container">
          ' . $entry['icon'] . '
        </div>
        <p class="small-title text-black font-semibold">' . $entry['title'] . '</p>
      </div>
    ';
  }

  return $value;
}

// Sidebar
function app_panel_aside(): string
{
  global $entries;
  global $current_url;

  // Inicializamos el HTML del aside
  $links_html = '';
  foreach( $entries as $entry )
  {
    // Estilo para cada link
    $checked = $entry['link'] === $current_url ? 'bg-gray-200 rounded-md shadow-landing': '';

    // Le insertamos formato al link
    $links_html .= sprintf(
        '<div class="px-4 py-3 flex flex-col gap-2 items-center %s">
          <a href="%s">
            %s
          </a>
          <p class="text-sm text-gray-600 text-center">%s</p>
        </div>'
      , $checked
      , $entry['link']
      , $entry['icon'] ?? ''
      , $entry['title']
    );
  }

  // Encapsulamos el sidebar
  $value = '
    <!-- Sidebar -->
    <aside class="fixed h-full top-0 left-0 bg-transparent shadow-landing-reverse">
      <div class="w-24 flex flex-col mt-28 justify-center items-center">
        ' . $links_html . '
      </div>
    </aside>
  ';

  return $value;
}

/**
 * Devuelve el código SVG de un icono.
 *
 * @param string $name Nombre del icono.
 * @return string Código SVG del icono.
 */
function app_get_svg_icon( string $name ): string
{
  $icons = [
      'desktop'     => '<i class="text-3xl fa-light fa-house"></i>'
    , 'activities'  => '<i class="text-3xl fa-light fa-person-running"></i>'
    , 'account'     => '<i class="text-3xl fa-light fa-user"></i>'
    , 'schedule'    => '<i class="text-3xl fa-light fa-calendar-days"></i>'
    , 'pen'         => '<i class="text-3xl fa-light fa-pen"></i>'
    , 'cloud'       => '<svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/></svg>'
  ];

  return $icons[$name] ?? '';
}

function app_dropdown_panel(): string
{
  $dropdown = '
    <div id="dropdown_panel" class="hidden absolute top-16 bg-white text-black px-3 py-2 rounded-md shadow-landing">
      <ul class="space-y-3">
        <li>
          <a href="/account" class="hover:text-blue-500 transform transition duration-300">' . pl_label( 'my-account' ) . '</a>
        </li>
        <li>
          <a href="/login" class="hover:text-blue-500 transform transition duration-300">' . pl_label( 'log-out' ) . '</a>
        </li>
      </ul>
    </div>
  ';

  return $dropdown;
}

// Función para pasar de un formato 2024-08-05 a Aug 5, 24
function app_convert_date_format( $date ): string
{
  $date_object = DateTime::createFromFormat( 'Y-m-d', $date );

  if( $date_object === false )
    return "Invalid date";

  return $date_object->format( 'M d, Y' );
}

/**
 * Organiza un array de fechas en grupos de fechas consecutivas.
 *
 * @param array $dates Array de fechas en formato 'Y-m-d'.
 * @return array Devuelve un array de arrays, donde cada sub-array contiene fechas consecutivas.
 */
function app_organize_dates( array $dates ): array
{
  $value           = [];
  $organized_dates = [];

  // Insertamos en el flujo a todas las fechas
  foreach( $dates as $index => $date )
  {
    // Insertamos la primera fecha
    if( $index === 0 )
    {
      $organized_dates[] = $date;
      continue;
    }

    // Calculamos la diferencia de días entre las fechas
    $prev_date    = new DateTime( $dates[$index - 1] );
    $actual_date  = new DateTime( $date );
    $diff         = $prev_date->diff( $actual_date )->days;

    // Si la fecha actual es el día siguiente a la anterior, se añade al mismo grupo
    if( $diff == 1 )
      $organized_dates[] = $date;
    else
    {
      // Si no es consecutiva, guardamos el grupo actual y comenzamos uno nuevo
      $value[]          = $organized_dates;
      $organized_dates  = [$date];
    }
  }

  // Añadimos el último grupo, en caso de que exista
  if( !empty( $organized_dates ) ) 
    $value[] = $organized_dates;

  return $value;
}

?>