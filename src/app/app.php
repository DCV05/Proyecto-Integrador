<?php

// -------------------------------------------------------------------------------------
// Contantes globales de la aplicación
// -------------------------------------------------------------------------------------

global $entries;
$entries = [
  [
    'title'  => 'Activities',
    'link'   => '/activities',
    'icon'   => get_svg_icon( 'activities' )
  ],
  [
    'title'  => 'Account',
    'link'   => '/account',
    'icon'   => get_svg_icon( 'account' )
  ],
  [
    'title'  => 'Schedule',
    'link'   => '/schedule',
    'icon'   => get_svg_icon( 'schedule' )
  ]
];

// URL actual
global $current_url;
$current_url = $_SESSION['polaris']['url_relative'];
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

function app_panel_render_tree( $child_entry = null, $is_child = false ): string
{
  global $entries;
  $value = '';
  
  // Colores de los svg
  $colors = ['blue', 'green', 'orange'];

  // Iteramos cada ruta
  $entries = $child_entry ?? $entries;
  foreach( $entries as $entry_index => $entry )
  {
    $link = $entry['link'] ?? '';

    // Gestionamos los hijos del link
    // Si tiene hijos, los renderizamos usando esta misma función recursivamente
    if( !empty( $entry['childs'] ) )
    {
      $childs = array_reduce( $entry['childs'], function( $buffer, $child ): string {
        return !empty( $child['childs'] )
          ? app_panel_render_tree( [$child], true )
          : '<a href=' . $child['link'] . ' class="text-gray-600 hover:underline">' . $child['title'] . '</a>';
      }, '' );
    }
    else
      $childs = '';

    // Asignamos unos colores según el índice
    $color = $colors[$entry_index] ?? 'blue';
    if( !empty( $entry['icon'] ) )
    {
      $icon = '
        <div class="w-9 h-9 bg-' . $color . '-500 flex justify-center items-center rounded-lg shadow-landing white-svg p-1">
          ' . $entry['icon'] . '
        </div>
      ';
    }
    else
      $icon = '';

    $padding_x = !$is_child ? 'px-[1.125rem]' : '';
    $value .= '
      <div class="grid grid-cols-[auto_1fr] gap-2 ' . $padding_x . ' py-2 items-start">
        ' . $icon . '
        <div class="flex flex-col gap-2 text-gray-700 text-base">
          <a href="' . $link . '" class="body-text font-bold mt-1">' . $entry['title'] . '</a>
          ' . $childs . '
        </div>
      </div>
    ';
  }

  return $value;
}

function app_panel_heading(): string
{
  global $entries;
  global $current_url;

  $value = '';

  // Headings
  $colors = ['blue', 'green', 'orange'];
  foreach( $entries as $entry_index => $entry )
  {
    $color = $colors[$entry_index] ?? 'blue';
    if( $current_url === $entry['link'] )
    {
      $value = '
        <div class="flex flex-row gap-2 items-center flex-shrink-0">
          <div class="w-11 h-11 bg-' . $color . '-500 flex justify-center items-center rounded-lg shadow-landing white-svg p-1">
            ' . $entry['icon'] . '
          </div>
          <p class="small-title text-black font-semibold">' . $entry['title'] . '</p>
        </div>
      ';
    }
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
function get_svg_icon( string $name ): string
{
  $icons = [
      'tutor'       => '<svg width="40" height="40" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M736 704 272 704c-8.832 0-16-7.152-16-16l0-16.048c0-92.832 56.496-178.272 142.752-219.232-17.84-23.632-27.168-53.712-27.168-88.336 0-78.384 63.536-142.16 141.616-142.16 77.776 0 141.056 63.776 141.056 142.16 0 32.208-12.144 63.888-33.904 89.76C701.056 494.464 752 577.52 752 671.952L752 688C752 696.848 744.848 704 736 704zM288 672l432-0.048c0-89.328-52.192-166.848-132.944-197.488-5.264-2-9.088-6.608-10.08-12.144-0.992-5.536 1.008-11.184 5.232-14.88 25.456-22.176 40.048-52.448 40.048-83.04 0-60.736-48.928-110.16-109.056-110.16-60.448 0-109.616 49.408-109.616 110.16 0 34.672 11.392 63.136 32.944 82.304 4.208 3.728 6.128 9.408 5.088 14.928s-4.912 10.096-10.192 12.032C345.648 505.248 288 584.928 288 671.952L288 672z"  /><path d="M224 656 144 656c-8.832 0-16-7.152-16-16 0-79.072 46.96-151.6 118.912-187.024-14.208-20.208-21.84-45.92-21.84-74.528 0-68.656 53.92-124.512 120.208-124.512 8.832 0 16 7.168 16 16s-7.168 16-16 16c-48.64 0-88.208 41.504-88.208 92.512 0 28.672 9.616 53.024 27.056 68.56 4.208 3.728 6.128 9.408 5.088 14.928-1.04 5.536-4.912 10.096-10.192 12.032-66.032 24.304-111.968 83.376-118.288 150.032L224 624c8.832 0 16 7.152 16 16S232.832 656 224 656z"  /><path d="M864 656l-80 0c-8.848 0-16-7.152-16-16s7.152-16 16-16l63.232 0c-6.352-66.832-52.176-125.808-117.984-150.032-5.264-1.952-9.136-6.512-10.192-12.032-1.04-5.52 0.88-11.184 5.072-14.928 17.648-15.696 27.36-40.048 27.36-68.56 0-51.008-39.664-92.512-88.384-92.512-8.848 0-16-7.168-16-16s7.152-16 16-16c66.384 0 120.384 55.856 120.384 124.512 0 28.544-7.712 54.256-22.08 74.544C833.184 488.4 880 560.88 880 639.76 880 648.608 872.848 656 864 656z"  /><path d="M511.968 990.624c-266.112 0-482.624-216.496-482.624-482.624 0-266.112 216.512-482.624 482.624-482.624 266.128 0 482.624 216.512 482.624 482.624C994.592 774.128 778.096 990.624 511.968 990.624zM511.968 57.376c-248.48 0-450.624 202.144-450.624 450.624 0 248.464 202.144 450.624 450.624 450.624 248.464 0 450.624-202.16 450.624-450.624C962.592 259.52 760.432 57.376 511.968 57.376z"  /></svg>'
    , 'activities'  => '<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 511.913 511.913" xml:space="preserve"><path d="m503.306 111.184-.883-.883c-11.476-11.476-31.779-11.476-43.255 0l-45.903 46.786c-2.648 2.648-6.179 2.648-8.828 0L339.995 97.06c-6.179-5.297-14.124-8.828-22.952-8.828h-96.221c-2.648 0-4.414.883-5.297 1.766l-81.214 77.683c-11.476 12.359-12.359 31.779-1.766 43.255 5.297 6.179 13.241 9.71 22.069 9.71s16.772-3.531 22.069-9.71l57.379-60.91h20.303L120.188 300.094H37.209c-20.303 0-36.193 15.007-37.076 33.545-.883 9.71 2.648 19.421 9.71 26.483 7.062 6.179 15.89 10.593 25.6 10.593H159.03c2.648 0 4.414-.883 6.179-2.648l63.559-67.972 52.965 55.614-15.89 102.4c-4.414 17.655 4.414 34.428 19.421 41.49 5.297 2.648 9.71 3.531 15.007 3.531s10.593-.883 15.89-3.531c8.828-4.414 15.89-13.241 18.538-23.835l27.365-147.421c0-2.648-.883-6.179-2.648-7.945l-73.269-73.269 58.262-58.262 40.607 40.607c11.476 11.476 30.897 11.476 42.372 0l75.917-75.917c11.477-11.477 11.477-30.897.001-42.373m-12.359 30.014-75.917 75.917c-5.297 4.414-13.241 4.414-17.655 0l-46.786-46.786c-3.531-3.531-8.828-3.531-12.359 0l-70.621 70.621c-1.766 1.766-2.648 3.531-2.648 6.179s.883 4.414 3.531 7.062l75.917 75.917-26.483 142.124c-1.766 4.414-5.297 8.828-9.71 11.476-4.414 1.766-9.71 2.648-15.007 0-7.945-3.531-11.476-12.359-9.71-22.069l16.772-107.697c0-2.648-.883-5.297-2.648-7.062l-62.676-65.324c-1.766-1.766-3.531-2.648-6.179-2.648s-5.297.883-6.179 2.648l-67.09 71.503H35.444q-7.945 0-13.241-5.297c-2.648-3.531-4.414-7.945-4.414-13.241 0-9.71 8.828-16.772 19.421-16.772h86.51c2.648 0 4.414-.883 6.179-.883l150.069-167.724c2.648-2.648 2.648-6.179 1.766-9.71s-4.414-5.297-7.945-5.297h-44.138c-1.766 0-4.414.883-6.179 2.648l-60.028 63.559c-1.766 2.648-5.297 4.414-8.828 4.414s-7.062-1.766-9.71-4.414c-4.414-5.297-4.414-13.241.883-18.538l78.566-75.034h93.572c3.531 0 7.945 1.766 10.593 4.414l64.441 60.028c9.71 8.828 24.717 7.945 33.545-.883l45.904-46.786c4.414-4.414 13.241-4.414 17.655 0l.883.883c4.413 4.413 4.413 12.358-.001 16.772"/><path d="M406.202 114.715c29.131 0 52.966-23.834 52.966-52.966S435.333 8.784 406.202 8.784s-52.966 23.834-52.966 52.966 23.835 52.965 52.966 52.965m0-88.276c19.421 0 35.31 15.89 35.31 35.31s-15.89 35.31-35.31 35.31-35.31-15.89-35.31-35.31 15.89-35.31 35.31-35.31"/></svg>'
    , 'account'     => '<svg width="40" height="40" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M843.282963 870.115556c-8.438519-140.515556-104.296296-257.422222-233.908148-297.14963C687.881481 536.272593 742.4 456.533333 742.4 364.088889c0-127.241481-103.158519-230.4-230.4-230.4S281.6 236.847407 281.6 364.088889c0 92.444444 54.518519 172.183704 133.12 208.877037-129.611852 39.727407-225.46963 156.634074-233.908148 297.14963-0.663704 10.903704 7.964444 20.195556 18.962963 20.195556l0 0c9.955556 0 18.299259-7.774815 18.962963-17.73037C227.745185 718.506667 355.65037 596.385185 512 596.385185s284.254815 122.121481 293.357037 276.195556c0.568889 9.955556 8.912593 17.73037 18.962963 17.73037C835.318519 890.311111 843.946667 881.019259 843.282963 870.115556zM319.525926 364.088889c0-106.287407 86.186667-192.474074 192.474074-192.474074s192.474074 86.186667 192.474074 192.474074c0 106.287407-86.186667 192.474074-192.474074 192.474074S319.525926 470.376296 319.525926 364.088889z"  /></svg>'
    , 'schedule'    => '<svg width="40" height="40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 120.06" xml:space="preserve"><path d="M69.66 4.05c0-2.23 2.2-4.05 4.94-4.05s4.94 1.81 4.94 4.05v17.72c0 2.23-2.2 4.05-4.94 4.05s-4.94-1.81-4.94-4.05zm21.71 52.98c4.26 0 8.33.85 12.05 2.39 3.87 1.6 7.34 3.94 10.24 6.84s5.24 6.38 6.84 10.23c1.54 3.72 2.39 7.79 2.39 12.05s-.85 8.33-2.39 12.05c-1.6 3.87-3.94 7.34-6.84 10.24a31.7 31.7 0 0 1-10.23 6.84 31.4 31.4 0 0 1-12.05 2.39c-4.26 0-8.33-.85-12.05-2.39-3.87-1.6-7.34-3.94-10.24-6.84s-5.24-6.38-6.84-10.24a31.4 31.4 0 0 1-2.39-12.05c0-4.26.85-8.33 2.39-12.05 1.6-3.87 3.94-7.34 6.84-10.24s6.38-5.24 10.23-6.84c3.72-1.53 7.78-2.38 12.05-2.38m-2.36 18.34c0-.76.31-1.45.81-1.95s1.19-.81 1.96-.81 1.46.31 1.96.81.81 1.19.81 1.96v14.74l11.02 6.54.09.06c.61.39 1.01.98 1.17 1.63.17.68.09 1.42-.28 2.06l-.02.03a.4.4 0 0 1-.07.1c-.39.6-.98 1-1.62 1.16-.68.17-1.42.09-2.06-.28l-12.32-7.29a2.85 2.85 0 0 1-1.05-.99 2.7 2.7 0 0 1-.41-1.43zm20.74-5.21c-2.4-2.4-5.26-4.33-8.43-5.64-3.06-1.27-6.42-1.96-9.95-1.96s-6.89.7-9.95 1.96a26 26 0 0 0-8.43 5.64c-2.4 2.4-4.33 5.26-5.64 8.43a25.9 25.9 0 0 0-1.96 9.95c0 3.53.7 6.89 1.96 9.95 1.31 3.17 3.24 6.03 5.64 8.43s5.26 4.33 8.43 5.64c3.06 1.27 6.42 1.96 9.95 1.96s6.89-.7 9.95-1.96c3.17-1.31 6.03-3.24 8.43-5.64a25.9 25.9 0 0 0 7.61-18.38c0-3.53-.7-6.89-1.96-9.95a26.1 26.1 0 0 0-5.65-8.43m-96.3-12.8c-.28 0-.53-1.23-.53-2.74s.22-2.73.53-2.73h13.48c.28 0 .53 1.23.53 2.73 0 1.51-.22 2.74-.53 2.74zm21.49 0c-.28 0-.53-1.23-.53-2.74s.22-2.73.53-2.73h13.48c.28 0 .53 1.23.53 2.73 0 1.51-.22 2.74-.53 2.74zm21.49 0c-.28 0-.53-1.23-.53-2.74s.22-2.73.53-2.73h13.48c.28 0 .53 1.22.53 2.72a41 41 0 0 0-3.89 2.75zM13.48 73.04c-.28 0-.53-1.23-.53-2.74s.22-2.74.53-2.74h13.48c.28 0 .53 1.23.53 2.74s-.22 2.74-.53 2.74zm21.49 0c-.28 0-.53-1.23-.53-2.74s.22-2.74.53-2.74h13.48c.28 0 .53 1.23.53 2.74s-.22 2.74-.53 2.74zM13.51 88.73c-.28 0-.53-1.23-.53-2.74s.22-2.74.53-2.74h13.48c.28 0 .53 1.23.53 2.74s-.22 2.74-.53 2.74zm21.49 0c-.28 0-.53-1.23-.53-2.74s.22-2.74.53-2.74h13.48c.28 0 .53 1.23.53 2.74s-.22 2.74-.53 2.74zM25.29 4.05c0-2.23 2.2-4.05 4.94-4.05s4.94 1.81 4.94 4.05v17.72c0 2.23-2.21 4.05-4.94 4.05-2.74 0-4.94-1.81-4.94-4.05zM5.44 38.74h94.08v-20.4c0-.7-.28-1.31-.73-1.76s-1.09-.73-1.76-.73h-9.02c-1.51 0-2.74-1.23-2.74-2.74s1.23-2.74 2.74-2.74h9.02c2.21 0 4.19.89 5.64 2.34a7.93 7.93 0 0 1 2.34 5.64v32.39c-1.8-.62-3.65-1.12-5.55-1.49v-5.06h.06H5.44v52.83c0 .7.28 1.31.73 1.76s1.09.73 1.76.73h44.71c.51 1.9 1.15 3.75 1.92 5.53H7.98c-2.2 0-4.19-.89-5.64-2.34A7.9 7.9 0 0 1 0 97.07V18.36c0-2.2.89-4.19 2.34-5.64a7.93 7.93 0 0 1 5.64-2.34h9.63c1.51 0 2.74 1.23 2.74 2.74s-1.23 2.74-2.74 2.74H7.98c-.7 0-1.31.28-1.76.73s-.73 1.09-.73 1.76v20.4h-.05zm37.63-22.89c-1.51 0-2.74-1.23-2.74-2.74s1.23-2.74 2.74-2.74h18.36c1.51 0 2.74 1.23 2.74 2.74s-1.23 2.74-2.74 2.74z"/></svg>'
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