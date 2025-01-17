<?php

// Clase enrutador
class Router
{
  /** @var array Arreglo que contiene las rutas registradas. */
  public array $routes;

  /** @var string URI calculada a partir de la URL del cliente. */
  public string $uri;

  /** @var bool Indica si la petición es una solicitud AJAX. */
  public bool $ajax;

  /**
   * Constructor de la clase Router.
   *
   * Inicializa las propiedades, calcula la URI de la solicitud actual
   * y configura el controlador correspondiente basado en la base de datos.
   *
   * @throws Exception Si no se encuentra una página válida en la base de datos.
   */
  public function __construct()
  {   
    $db = new pl_model();

    // ------------------------------------------------------------------------------
    // Búsqueda de la URI
    // ------------------------------------------------------------------------------

    // Inicializamos el array de rutas y si es una ruta ajax
    $this->routes = [];
    $this->ajax   = false;

    // Calculamos la URI a partir de la URL del cliente
    $url_parts = explode( '/', $_SESSION['polaris']['url_base'] ); 
    $url_parts = array_values(
      array_filter( $url_parts )
    );

    // Si se trata de una llamada AJAX
    if( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) )
    {
      // Calculamos el destinatario de la llamada AJAX
      $ajax_target = pl_get( 'cn' );

      // Definimos la URI de la llamada
      $uri = $ajax_target;
      $this->ajax = true;
    }
    elseif( !empty( $url_parts[0] ) )
      $uri = $_SESSION['polaris']['url_base'];
    else
      $uri = '/index';

    $this->uri = $uri;

    // Buscamos la página en la DB
    $sql = 'select * from polaris_pages where url = "' . $this->uri . '" limit 1';
    $rows = $db->pl_query( $sql );

    /*
      Array
      (
        [0] => Array
          (
            [page_id] => 1
            [url] => Index
            [page_title] => Index
            [file] => Index/Index
            [title_seo] => Index
          )
      )
    */

    // ------------------------------------------------------------------------------
    // Creación del nombre del controlador
    // ------------------------------------------------------------------------------

    // Comprobamos el valor de la URL
    if( !empty( $rows[0] ) && $this->uri == $rows[0]['url'] )
    {
      // Calculamos el nombre de la clase
      $class_name = array_reduce(

        // Dividimos la url y cambiamos los "/char" por "Char"
        explode( '/', $uri ),
        function ( $buffer, $part ): string {
          return $buffer . ucfirst( $part );
        },
        ''
      );

      // Añadimos un controlador al array final de la ruta
      $controller_name = $class_name . 'Controller@index';
      $this->routes[] = [
          $uri    => $controller_name
        , 'file'  => $rows[0]['file']
      ];

      // Definimos el nombre del controlador en la sesión
      $rows[0]['controller_anme']   = ucfirst( $uri ) . 'Controller';
      $_SESSION['polaris']['page']  = $rows[0];
    }
    else
      pl_redirect( '/index' );
    
    $db->close();
  }
}

?>