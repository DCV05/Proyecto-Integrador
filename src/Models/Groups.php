<?php

class Groups
{
  private Model $db;

  public function __construct()
  {
    $this->db = new Model();
  }

  /**
   * Obtiene todas las filas de la tabla `groups`.
   * 
   * @return array Lista de actividades o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = 'select * from ' . DB_PROJECT . '.groups';
    return $this->db->pl_query_prepared( $sql, [], true );
  }

  /**
   * Obtiene una fila específica de la tabla `groups` según `monitor_id`.
   * 
   * @param int $monitor_id Identificador de la actividad.
   * @return array Datos de la actividad si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int $monitor_id ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.groups
      where
        monitor_id = ?
    ';
    $params = [$this->db->esc( $monitor_id )];
    return $this->db->pl_query_prepared( $sql, $params, true );
  }
}