<?php

/**
 * Clase MySQL
 */
class pl_model extends mysqli
{
  public string $db_name;

  /**
   * Creamos un constructor para poder crear instancias de la conexión
   * @param string $db_name
   */
  public function __construct( string $db_name = DB_NAME )
  {
    $this->db_name = $db_name;

    // Configuramos mysqli para que lanze excepciones
    mysqli_report( MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR );

    // Intentamos crear una instancia de MySQL
    try
    {
      // Creamos una instancia de MySQL con UTF-8
      parent::__construct( DB_SERVER, DB_USER, DB_PASSWORD, $this->db_name );      
      $this->set_charset( 'utf8' );

      // Manejo de errores
      if( $this->connect_error )
        die( "Connection error {$this->connect_error}" );
    }
    
    // Captura de excepciones en la conexión con la base de datos
    catch( mysqli_sql_exception $mse ) // Capturamos excepciones de mysqli
    {
      throw new Exception( "Error during the connection with MySQLI {$mse->getMessage()}" );
    }
    catch( Exception $e )
    {
      throw new Exception( "Error {$e->getMessage()}" );
    }
    catch( Error $e ) // Captura de errores fatales de versiones mayores a PHP 7.0
    {
      throw new Exception( "Fatal error {$e->getMessage()}" );
    }
  }

  /**
   * Realizar consulta SQL a MySQL
   * @param string $sql
   * @return array<array|bool|null> $value
   * */ 
  public function pl_query( string $sql ): array|bool|null
  {
    // Inicializamos el array a devolver
    $value = [];

    try
    {
      if( $this->real_query( $sql ) && $data = $this->use_result() )
      {
        // Guardamos el resultado en el array a devolver
        while( $row = $data->fetch_assoc() )
          $value[] = $row;

        // Liberamos recursos
        $data->free();

        // Si hay solo un resultado, devolvemos la primera posición directamente
        return $value;
      }
    }
    catch( mysqli_sql_exception | Exception $e ) // Capturamos la excepción
    {
      print "\n\nError: {$e->getMessage()}\n\n";
    }

    return $value;
  }

  /**
   * Método para cerrar la sesión de MySQL
   */ 
  public function pl_close(): void
  {
    $this->close();
  }

  /**
   * Función para escapar caracteres
   * @param string $str
   * @return string $escaped_str
   *  */ 
  public function pl_esc( string $str ): string
  {
    // Escapamos el string
    $str = htmlspecialchars( $str, ENT_QUOTES, 'UTF-8' );
    $escaped_str = $this->real_escape_string( $str );

    return $escaped_str;
  }

   /**
   * Función para capturar el último ID insertado
   * @param mysqli $db
   * @return mixed $value
   *  */ 
  public function pl_db_last_id(): mixed
  {
    // Buscamos el nuevo ID
    $sql = 'select last_insert_id() as last_id';
    $query = $this->query( $sql );
    if( $query && $row = $query->fetch_assoc() )
    {
      $value = $row['last_id'];
      $query->close();
    }
    else
      $value = 0;
    
    return $value;
  }

  /**
   * Función para imprimir información de los campos de una tabla
   * @param string $table_name
   * @return string $error_fields
   */ 
  public function pl_describe( string $table_name ): array
  {
    // Inicializamos el array de errores a devolver
    $error_fields = [];

    // Consulta SQL de la tabla
    $sql    = "describe " . $this->db_name . ".{$table_name}";
    $result = $this->pl_query( $sql );

    // Iteramos cada resultado
    foreach( $result as $key => $value )
      $error_fields[] = $value['Field'];

    return $error_fields;
  }

  /**
   * Función para añadir una columna en una tabla
   * Se debe de especificar la tabla, el nombre de la columna, el tipo de dato y su capacidad (opcional)
   * @param string $table_name
   * @param string $col_name
   * @param string $col_type
   * @param string $col_capacity
   */
  public function pl_migration_add_column( string $table_name, string $col_name, string $col_type, string $col_capacity = null ): int
  {
    // Comprobamos si existe ua columna con ese nombre
    $table_fields = $this->pl_describe( $table_name );
    $in_table     = false;

    // Si el campo aparece en la tabla, lo mostramos
    foreach( $table_fields as $key => $value )
    {
      if( $col_name == $value )
        $in_table = true;
    }

    // En el caso de que no esté en la tabla, añadimos la columna
    if( $in_table == false )
    {
      $str_capacity = '';

      // Si tiene capacidad, la añadimos en el SQL
      if( $col_capacity != null )
        $str_capacity = "({$col_capacity})";
  
        $sql = "alter table " . $this->db_name . ".{$table_name} add {$col_name} {$col_type}{$str_capacity}";
       
      $this->pl_query( $sql );

      return 1;
    }
    else
    {
      print "\n\nThe field <b>{$col_name}</b> already exists <b>{$table_name}</b>";
      return 0;
    }
  }

  /**
   * Función para eliminar una columna de una tabla
   * @param string $table_name
   * @param string $col_name
   *  */ 
  public function pl_migration_remove_column( string $table_name, string $col_name ): int
  {
    $in_table = false;

    // Capturamos los campos de la tabla
    $table_fields = $this->pl_describe( $table_name );

    // Iteramos los campos y comprobamos si estamos intentando eliminar una columna no existente
    foreach( $table_fields as $none => $value )
    {
      if( $value == $col_name )
        $in_table = true;
    }

    // Si la columna existe en la tabla, la devolvemos
    if( $in_table )
    {
      $sql = "alter table " . $this->db_name . ".{$table_name} drop column {$col_name}";
      $this->pl_query( $sql );
      return 1;
    }
    else
    {
      print "\n\nThe field <b>{$col_name}</b> does not exist in <b>{$table_name}</b>";
      return 0;
    }
  }

  /**
   * Función para crear una tabla
   * @param string $table_name
   * @param array<string,array> $columns
   */
  public function pl_migration_create_table( string $table_name, array $columns ): void
  {
    // Inicializamos el array de campos
    $combined_fields = [];

    // Iteramos los campos del array insertado en las opciones
    foreach( $columns as $column )
    {
      // Insertamos los campos de las opciones dentro del array de campos
      foreach( $column as $column_name => $options )
        $combined_fields[] = $column_name . ' ' . $options;
    }

    // Pasamos de array a string
    $combined_fields = implode( ', ', $combined_fields );

    $sql = "create table {$table_name} ({$combined_fields})";
    $this->pl_query( $sql );
  }

  /**
   * Función para borrar una tabla
   * @param string $table_name
   */
  public function pl_migration_drop_table( $table_name ): void
  {
    $sql = "drop table if exists " . $this->db_name . ".{$table_name}";
    $this->pl_query( $sql );
  }

  /**
   * Función para crear una base de datos
   * @param string $db_name
   */
  public function pl_migration_create_database( $db_name ): void
  {
    $sql = "create database `{$db_name}`";
    $this->pl_query( $sql );
  }

  /**
   * Función para borrar una bas de datos
   * @param string $db_name
   */
  public function pl_migration_drop_database( string $db_name ): void
  {
    $sql = "drop database {$db_name}";
    $this->pl_query( $sql );
  }
}

?>