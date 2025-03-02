<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../src/app/Model.php';

final class ModelTest extends TestCase
{
  private Model $model;

  protected function setUp(): void
  {
    // Asegúrate de que tienes definidas las constantes DB_SERVER, DB_USER, DB_PASSWORD, DB_SYS
    // por ejemplo, en tu polaris.php.
    $this->model = new Model( DB_SYS );
  }

  public function testConnectionIsEstablished(): void
  {
    $this->assertNotNull( $this->model, 'No se pudo instanciar el Model' );
    $this->assertTrue( $this->model->ping(), 'No hay conexión activa con MySQL' );
  }

  public function testSimpleSelectQuery(): void
  {
    // Asumiendo que exista la tabla polaris_pages y que tenga registros
    $sql    = 'select * from polaris_pages limit 5';
    $result = $this->model->pl_query( $sql, true );

    $this->assertIsArray( $result, 'pl_query no devolvió un array' );
  }

  public function testPreparedQueryWithParameters(): void
  {
    // Asumiendo que polaris_pages tiene una columna url
    $sql    = 'select page_id, url from polaris_pages where url = ?';
    $params = ['/Index'];
    $result = $this->model->pl_query_prepared( $sql, $params, true );

    $this->assertIsArray( $result, 'La consulta preparada no devolvió un array' );
    $this->assertNotEmpty( $result, 'No se encontró ninguna fila con url = /index' );
    $this->assertEquals( '/index', $result[0]['url'] ?? '', 'El primer registro no coincide con /index' );
  }

  public function testMigrationCreateAndDropTable(): void
  {
    $table_name = 'test_table_' . uniqid();

    // Creamos la tabla
    $this->model->pl_migration_create_table( $table_name, [
        ['id'   => 'INT AUTO_INCREMENT PRIMARY KEY']
      , ['name' => 'VARCHAR(100) NOT NULL' ]
    ] );

    // Verificamos que la tabla exista
    $sql    = 'show tables like "' . $table_name . '"';
    $tables = $this->model->pl_query( $sql, true );
    $this->assertNotEmpty( $tables, 'La tabla ' . $table_name . ' no se creó correctamente' );

    // Borramos la tabla
    $this->model->pl_migration_drop_table( $table_name );

    // Verificamos que ya no exista
    $tables_after_drop = $this->model->pl_query( $sql, true );
    $this->assertEmpty( $tables_after_drop, 'La tabla ' . $table_name . ' no se eliminó correctamente' );
  }
}