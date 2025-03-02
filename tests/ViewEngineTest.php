<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/app/sdk.php';
require_once __DIR__ . '/../src/app/ViewEngine.php';

class FakeController
{
  public string $controller_property = 'Valor en la propiedad del controlador';

  public array $controller = [
    'subkey' => 'Valor dentro de un array en el controlador'
  ];

  // Este método se llamará como [[ func | function_test ]]
  public function function_test(): string
  {
    return 'Resultado de function_test()';
  }
}

final class ViewEngineTest extends TestCase
{
  private string $template_path;

  protected function setUp(): void
  {
    // Inicializamos la sesión con POLARIS
    pl_start();

    // Definimos un label real en la sesión
    $_SESSION['labels'] = [
      'hello_world' => [
        'es' => 'Hola Mundo',
        'en' => 'Hello World'
      ],
      'test' => [
        'es' => 'Prueba',
        'en' => 'Test'
      ]
    ];

    // Definimos el idioma por defecto
    if( !defined( 'DEF_LANG' ) )
      define( 'DEF_LANG', 'es' );

    // Creamos un archivo temporal .html que usaremos como plantilla de prueba
    $this->template_path = __DIR__ . '/test_template_' . uniqid() . '.html';
    $template_content = '
      <html>
      <head>
        <title>[[ label | hello_world ]]</title>
      </head>
      <body>
        <h1>[[ label | test ]]</h1>
        <p>[[ controller_property ]]</p>
        <div>[[ controller.subkey ]]</div>
        <div>[[ func | function_test ]]</div>
      </body>
      </html>
    ';

    file_put_contents( $this->template_path, $template_content );
  }

  protected function tearDown(): void
  {
    // Borramos el archivo temporal
    if( file_exists( $this->template_path ) )
      unlink( $this->template_path );
  }

  public function testViewEngineCompile(): void
  {
    // Simulamos un controlador con propiedades y métodos
    $controller = new FakeController();

    // Instanciamos el ViewEngine
    $view_engine = new ViewEngine(
        $this->template_path
      , $controller
      , 'controller_1'
      , 'hash_controller_1'
    );

    // Llamamos internamente a compile (normalmente lo llama render_template)
    $template_original = file_get_contents( $this->template_path );
    $compiled = $view_engine->compile( $template_original );

    // Verificamos que haya hecho los reemplazos de label
    $this->assertStringNotContainsString( '[[ label | hello_world ]]', $compiled );
    $this->assertStringContainsString( 'Hola Mundo', $compiled );

    // Verificamos que haya sustituido la propiedad
    $this->assertStringContainsString( 'Valor en la propiedad del controlador', $compiled );

    // Verificamos la navegación: [[ controller.subkey ]]
    $this->assertStringContainsString( 'Valor dentro de un array en el controlador', $compiled );

    // Verificamos la función: [[ func | function_test ]]
    $this->assertStringContainsString( 'Resultado de function_test()', $compiled );
  }

  public function testRenderTemplateIncludesScriptTag(): void
  {
    // Simulamos el mismo controlador
    $controller = new FakeController();

    $view_engine = new ViewEngine(
        $this->template_path
      , $controller
      , 'controller_2'
      , 'hash_controller_2'
    );

    // Capturamos la salida de render_template
    ob_start();
    $view_engine->render_template();
    $output = ob_get_clean();

    // Verificamos que el <script> con PL_CH se inyectó
    $this->assertStringContainsString(
        '<script>const PL_CH="'
      , $output
      , 'No se encontró la variable PL_CH en el script'
    );

    // Verificamos que cierra la etiqueta </script>
    $this->assertStringContainsString(
        '</script>'
      , $output
      ,'No se cierra la etiqueta <script> para PL_CH'
    );

    // Verificamos que no esté vacío
    preg_match( '/PL_CH="([^"]+)"/', $output, $matches );
    $this->assertNotEmpty( $matches, 'No se encontró la expresión PL_CH="..."' );
    $this->assertNotEmpty( $matches[1], 'La variable PL_CH está vacía' );
  }
}