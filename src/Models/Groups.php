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
  public function GetRows( string $where = '' ): array
  {
    $sql = 'select * from ' . DB_PROJECT . '.groups ' . $where;
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

  /**
   * Obtiene una fila específica de la tabla `groups` según `group_id2`.
   * 
   * @param string $group_id Identificador de la actividad.
   * @return array Datos de la actividad si existe, o un array vacío si no hay resultados.
   */
  public function GetGroupGID( int|string $group_id ): array
  {
    $field = is_numeric( $group_id ) ? 'group_id' : 'group_id2';

    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.groups
      where
        ' . $field . ' = ?
    ';
    $params = [$this->db->esc( $group_id )];
    return $this->db->pl_query_prepared( $sql, $params, true );
  }
}