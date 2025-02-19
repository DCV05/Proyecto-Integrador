<?php

// -------------------------------------------------------------------------------------
// Contantes globales de la aplicación
// -------------------------------------------------------------------------------------

global $entries, $folder, $role;

$roles = [
    0 => 'tutor'
  , 1 => 'monitor'
  , 2 => 'admin'
];

// Capturamos el rol del usuario
$role   = intval( $_SESSION['app']['user']['role'] ?? null );
$folder = $roles[$role] ?? 'tutor';

$entries = [
  [
    'title' => pl_label( 'desktop' ),
    'link'  => '/' . $folder . '/desktop',
    'icon'  => app_get_svg_icon( 'desktop' )
  ],
  [
    'title' => pl_label( 'activities' ),
    'link'  => '/activities',
    'icon'  => app_get_svg_icon( 'activities' )
  ]
];

if( $role === 2 )
{
  $entries[] = [
      'title' => pl_label( 'participants' )
    , 'link'  => '/participants'
    , 'icon'  => app_get_svg_icon( 'kids' )
  ];

  $entries[] = [
      'title' => pl_label( 'users' )
    , 'link'  => '/users'
    , 'icon'  => app_get_svg_icon( 'users' )
  ];

  $entries[] = [
      'title' => pl_label( 'finances' )
    , 'link'  => '/' . $folder . '/finances'
    , 'icon'  => app_get_svg_icon( 'finances' )
  ];
}

if( $role === 1 )
{  
  $entries[] = [
      'title' => pl_label( 'participants' )
    , 'link'  => '/participants'
    , 'icon'  => app_get_svg_icon( 'kids' )
  ];

  $entries[] = [
      'title' => pl_label( 'users' )
    , 'link'  => '/users'
    , 'icon'  => app_get_svg_icon( 'users' )
  ];

  $entries[] = [
      'title' => pl_label( 'schedule' )
    , 'link'  => '/' . $folder . '/schedule'
    , 'icon'  => app_get_svg_icon( 'schedule' )
  ];
}

// Añadimos el apartado de cuenta al final del sidebar
$entries[] = [
    'title' => pl_label( 'account' )
  , 'link'  => '/' . $folder . '/account'
  , 'icon'  => app_get_svg_icon( 'account' )
];

// Definimos las entradas del encabezado (heading_entries)
global $heading_entries;
$heading_entries = array_merge( $entries, [
  [
    'title'           => pl_label( 'account' ),
    'link'            => '/' . $folder . '/account',
    'icon'            => app_get_svg_icon( 'account' ),
    'layout_buttons'  => false
  ],
  [
    'title'           => pl_label( 'activities' ),
    'link'            => '/activities',
    'icon'            => app_get_svg_icon( 'activities' ),
    'layout_buttons'  => true
  ],
  [
    'title'           => '<a href="/activities" class="hover:underline">' . pl_label( 'activities' ) . '</a>' . '/' . pl_label( 'activity' ),
    'link'            => '/activity',
    'icon'            => app_get_svg_icon( 'activities' ),
    'layout_buttons'  => false
  ],
  [
    'title'           => pl_label( 'participants' ),
    'link'            => '/participants',
    'icon'            => app_get_svg_icon( 'account' ),
    'layout_buttons'  => false
  ],
  [
    'title'           => '<a href="/activities" class="hover:underline">' . pl_label( 'participants' ) . '</a>' . '/' . pl_label( 'participant' ),
    'link'            => '/participant',
    'icon'            => app_get_svg_icon( 'account' ),
    'layout_buttons'  => false
  ],
  [
    'title'           => pl_label( 'schedule' ),
    'link'            => '/' . $folder . '/schedule',
    'icon'            => app_get_svg_icon( 'schedule' ),
    'layout_buttons'  => false
  ],
  [
    'title'           => pl_label( 'attendance' ),
    'link'            => '/' . $folder . '/attendance',
    'icon'            => app_get_svg_icon( 'attendance' ),
    'layout_buttons'  => false
  ],
  [
    'title'           => pl_label( 'users' ),
    'link'            => '/users',
    'icon'            => app_get_svg_icon( 'users' ),
    'layout_buttons'  => false
  ],
] );
  
// Colores de los svg
global $colors;
$colors = ['blue', 'emerald', 'cyan', 'orange', 'indigo', 'lime'];

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

function app_layout_buttons(): string
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
    $bold = $current_url == $link ? 'font-bold bg-gray-100' : '';
    $icon = !empty( $entry['icon'] ) ? '<div class="w-9 h-9 bg-' . $color . '-500 shadow-landing white-svg text-i-2xl tree-icon-container">' . $entry['icon'] . '</div>' : '';

    // Encapsulamos
    $value .= sprintf(
      '<a href="%s" class="grid grid-cols-[auto_1fr] gap-2 px-[0.7rem] py-2 items-start hover:bg-gray-100 transform transition duration-300 mx-2 rounded-lg %s">
        %s
        <p class="text-base mt-1">%s</p>
      </a>',
      $link,
      $bold,
      $icon,
      $entry['title']
    );
  }

  return $value;
}

function app_panel_hamburger_menu(): string
{
  global $entries, $current_url, $colors;

  $value = '
    <button id="hamburger_button" class="md:hidden bg-blue-500 text-white px-2 py-1.5 rounded-lg z-50">
      ' . app_get_svg_icon( 'menu' ) . '
    </button>

    <div id="hamburger_menu" class="fixed top-0 left-0 w-full h-fit bg-white shadow-lg transform -translate-x-full transition-transform duration-300 overflow-y-auto" style="z-index: 1000;">
      <div class="relative">
        <button id="close_hamburger" class="absolute top-0 right-4 text-4xl p-2" style="z-index: 1000;">&times;</button>
      </div>
      <div class="p-4 space-y-2 mt-10">
  ';

  // Generamos las opciones del menú usando `app_panel_render_tree()`
  foreach( $entries as $entry_index => $entry )
  {
    $link   = $entry['link'] ?? '';
    $color  = $colors[$entry_index] ?? 'blue';
    $bold   = $current_url == $link ? 'font-bold bg-gray-100' : '';
    $icon   = !empty( $entry['icon'] ) ? '<div class="w-9 h-9 bg-' . $color . '-500 shadow-landing white-svg text-i-2xl tree-icon-container">' . $entry['icon'] . '</div>' : '';

    $value .= sprintf(
      '<a href="%s" class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 transform transition duration-300 rounded-lg %s">
        %s
        <p class="text-base">%s</p>
      </a>',
      $link, $bold, $icon, $entry['title']
    );
  }

  $value .= '
      </div>
    </div>
  ';

  return $value;
}

function app_panel_interface(): string
{
  $html = '
    <div class="background"></div>
    <div class="overlay"></div>

    <nav id="main-navbar" class="fixed w-full left-0 top-0 bg-transparent shadow-landing-reverse z-50">
      <div class="h-16 flex flex-row gap-4 items-center">

        <div class="flex flex-col flex-1 items-start p-4">
          <a class="inline-flex" href="/" title="kodalogic">
            <span class="subtitle lufga-regular font-normal bg-clip-text text-transparent bg-gradient-to-tr from-blue-500 to-[#5560f5]">' . pl_label( 'campament' ) . '</span>
          </a>
        </div>

        <div class="hidden md:flex flex-row flex-1 items-center gap-4 p-4">
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="32" height="32" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </div>
        
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="32" height="32" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>

          <div class="relative w-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="query" id="query" placeholder="Search" class="text-base rounded-md py-2 pl-10 pr-3 w-full border-0 shadow-panel bg-gray-100 text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
          </div>
        </div>

        <div class="flex flex-col flex-1 items-end p-4 relative">
          <div class="flex flex-row gap-4">
            <button id="dropdown_button">
              ' . $_SESSION['app']['user']['user_image'] . '
            </button>
          </div>

          ' . app_dropdown_panel() . '
        </div>

      </div>
    </nav>
    
    <!--' . app_panel_aside() . '-->

    <nav class="fixed left-0 md:left-64 top-16 bg-white w-full md:w-[calc(100%-16rem)] z-20 shadow-landing">
      <div class="h-16 flex flex-row gap-4 items-center space-x-4 px-2 py-3">
        <div class="flex flex-row gap-4 flex-1 justify-between items-center p-2.5">
          ' . app_panel_heading() . '
          ' . app_panel_hamburger_menu() . '
        </div>
      </div>
    </nav>

    <aside class="hidden md:block fixed h-full left-0 top-16 bg-white shadow-landing-reverse z-50">
      <div class="w-64 flex flex-col">
        
        <div class="overflow-y-auto mt-2">
          ' . app_panel_render_tree() . '      
        </div>

      </div>
    </aside>
  ';

  return $html;
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

    if( isset( $entry['layout_buttons'] ) && $entry['layout_buttons'] === true )
    {
      $layout_buttons = '
        <div class="flex items-center">
          ' .  app_layout_buttons() . '
        </div>
      ';
    }
    else
      $layout_buttons = '';

    // Estilos del heading
    $color = $colors[$entry_index] ?? 'blue';

    $value = '
      <div class="flex justify-between items-center w-full">
        <div class="flex flex-row gap-2 items-center flex-shrink-0">
          <div class="w-11 h-11 bg-' . $color . '-500 shadow-landing white-svg tree-icon-container">
            ' . $entry['icon'] . '
          </div>
          <p class="small-title text-black font-semibold">' . $entry['title'] . '</p>
        </div>

        ' . $layout_buttons . '

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
    , 'attendance'  => '<i class="text-3xl fa-light fa-clipboard-user"></i>'
    , 'finances'    => '<i class="text-3xl fa-light fa-coins"></i>'
    , 'reports'     => '<i class="text-3xl fa-light fa-chart-line"></i>'
    , 'pen'         => '<i class="text-3xl fa-light fa-pen"></i>'
    , 'trash'       => '<i class="text-2xl fa-light fa-trash"></i>'
    , 'email'       => '<i class="text-3xl fa-light fa-envelope"></i>'
    , 'plus'        => '<i class="text-4xl fa-regular fa-plus"></i>'
    , 'paper-icon'  => '<i class="text-xl fa-light fa-paper-plane"></i>'
    , 'exclamation' => '<i class="text-xl fa-light fa-circle-exclamation"></i>'
    , 'users'       => '<i class="text-3xl fa-light fa-users"></i>'
    , 'kids'        => '<i class="text-3xl fa-solid fa-child-reaching"></i>'
    , 'menu'        => '<i class="text-xl fa-solid fa-bars"></i>'
    , 'cloud'       => '<svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/></svg>'
  ];

  return $icons[$name] ?? '';
}

function app_dropdown_panel(): string
{
  global $folder;

  $dropdown = '
    <div id="dropdown_panel" class="hidden absolute top-16 bg-white text-black px-3 py-2 rounded-md shadow-landing">
      <ul class="space-y-3">
        <li>
          <a href="/' . $folder . '/account" class="hover:text-blue-500 transform transition duration-300">' . pl_label( 'my-account' ) . '</a>
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

function app_custom_input( string $name, string $type ): string
{
  $eye_icon = '';

  // Si el tipo es "password", añadimos el icono de ojo
  if( $type === 'password' )
  {
    $eye_icon = '
      <button type="button" class="toggle-password absolute right-3 top-11 transform -translate-y-1/2 text-gray-500" data-target="' . $name . '">
        <i class="fa-regular fa-eye"></i>
      </button>
    ';
  }

  $input_html = '
    <div class="relative">
      <label for="' . $name . '" class="custom-label">' . pl_label( $name ) . '</label>
      <input
        type="' . $type . '"
        name="' . $name . '"
        id="' . $name . '"
        placeholder="' . pl_label( $name . '_placeholder' ) . '"
        class="custom-input transform transition duration-300 mt-1 pr-10">
      ' . $eye_icon . '
    </div>
  ';

  return $input_html;
}

function app_custom_textarea( string $name, $value = '' ): string
{
  $textarea_html = '
    <div>
      <label for="' . $name . '" class="custom-label">' . pl_label( $name ) . '</label>
      <textarea
        id="' . $name . '"
        name="' . $name . '"
        class="custom-input transform transition duration-300 mt-1"
      >' . $value . '</textarea>
    </div>
  ';

  return $textarea_html;
}

// ----------------------------------------------------------------------------------------------------------------
// Landing
// ----------------------------------------------------------------------------------------------------------------

function app_landing_navbar( $center = false ): string
{
  $margin = $center == false
    ? 'ml-32'
    : 'mx-auto';

  $html = '
    <nav id="main-navbar" class="bg-transparent fixed top-0 w-full z-50 transform transition">
      <div class="flex items-center w-fit ' . $margin . ' space-x-12">
        
        <div class="py-5">
          <a class="inline-flex" href="/" title="Campament">
            <span class="subtitle lufga-regular font-normal bg-clip-text text-transparent bg-gradient-to-tr from-blue-500 to-[#5560f5]">
              ' . pl_label( 'campament' ) . '
            </span>
          </a>
        </div>

        <div class="py-3 flex flex-row items-center gap-12 text-gray-700">
          <a href="#activities" class="hover:text-blue-500 cursor-pointer transition">' . pl_label( 'activities' ) . '</a>
          <a href="/contact" class="hover:text-blue-500 cursor-pointer transition">' . pl_label( 'contact' ) . '</a>
          <a href="/login" class="apple-button-secondary transition bg-white">' . pl_label( 'login' ) . '</a>
        </div>

        <div class="flex flex-row items-center gap-12">
          <a href="/signup" class="apple-button transition">' . pl_label( 'signup' ) . '</a>
        </div>

      </div>
    </nav>
  ';

  return $html;
}

function app_landing_footer(): string
{
  $html = '
    <footer class="mx-auto mt-24 shadow-landing py-3">
      <div class="grid lg:grid-cols-5 gap-12 items-center mx-auto">
        <div class="lg:col-span-5 text-center">
          <a class="inline-flex" href="/" title="Campament">
            <span class="subtitle lufga-regular font-normal bg-clip-text text-transparent bg-gradient-to-tr from-blue-500 to-[#5560f5]">
              ' . pl_label( 'campament' ) . '
            </span>
          </a>
          <p class="text-sm text-gray-500 mt-4">&copy; 2024 Todos los derechos reservados.</p>
        </div>
      </div>
    </footer>
  ';

  return $html;
}

?>