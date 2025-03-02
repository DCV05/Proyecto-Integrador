<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';
final class KernelTest extends TestCase
{
  public function setUp(): void
  {
    pl_start();
  }

  public function testKernelConstantsAreDefined(): void
  {
    // Probamos que algunas constantes definidas en polaris.php existan
    $this->assertTrue( defined( 'BASE_PATH' ), 'Constante BASE_PATH no está definida' );
    $this->assertTrue( defined( 'MAIN_PATH' ), 'Constante MAIN_PATH no está definida' );
    $this->assertTrue( defined( 'APP_PATH' ),  'Constante APP_PATH no está definida' );
  }

  public function testSessionIsInitialized(): void
  {
    $this->assertNotNull( $_SESSION, 'La sesión no se ha iniciado correctamente' );
  }
}