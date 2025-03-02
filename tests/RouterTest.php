<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../src/app/sdk.php';
require_once __DIR__ . '/../src/app/Router.php';
require_once __DIR__ . '/../src/app/Model.php';

final class RouterTest extends TestCase
{
  public function testRouterLoadsExistingRoute(): void
  {
    $_SESSION['polaris']['url_base'] = '/index';

    // Asegurarnos de que en DB exista un registro en polaris_pages con url = '/index'
    // y file = 'Index/Index'. De lo contrario, esta prueba fallará.
    // Creamos instancia del Router
    $router = new Router();

    $this->assertNotEmpty( $router->routes, 'No se detectó ninguna ruta' );
    $this->assertEquals( '/index', $router->uri, 'La URI calculada no es /index' );

    // Comprobamos que la primera ruta en $router->routes corresponde a /Index
    $first_route = $router->routes[0];
    $this->assertArrayHasKey( '/index', $first_route, 'No se encontró /Index como key en la primera ruta' );
    $this->assertEquals( 'IndexController@index', $first_route['/index'], 'El controlador esperado no coincide' );
  }

  public function testAjaxDetection(): void
  {
    // Simulamos una llamada AJAX
    $_GET['cn'] = 'Index'; // Normalmente en tu kernel usas cn para 'controller name'
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

    // Reiniciamos router
    $router = new Router();
    $this->assertTrue( $router->ajax, 'No se detectó AJAX correctamente' );
    $this->assertEquals( '/Index', $router->uri, 'La URI AJAX no fue /Index') ;
  }
}
