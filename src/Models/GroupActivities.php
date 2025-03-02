<?php

class GroupActivities
{
  private Model $db;

  public function __construct()
  {
    $this->db = new Model();
  }

  /**
   * Obtiene todas las filas de la tabla `group_activities`.
   * 
   * @return array Lista de relaciones grupo-actividad o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = '
      select
        ga.*, g.*
      from ' . DB_PROJECT . '.group_activities ga
      left join ' . DB_PROJECT . '.groups g on ga.group_id = g.group_id
      group by
        g.group_id2
    ';
    return $this->db->pl_query_prepared( $sql, [], true );
  }

  /**
   * Obtiene una fila específica de la tabla `group_activities` según `group_id` o `group_id2`.
   * 
   * @param int|string $group_id Identificador del grupo (ID numérico o ID alfanumérico).
   * @return array Datos de la relación grupo-actividad si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int|string $group_id ): array
  {
    $field = is_numeric( $group_id ) ? 'g.group_id' : 'g.group_id2';

    $sql = '
      select
          ga.* 
        , g.group_id
        , a.*
      from ' . DB_PROJECT . '.group_activities ga
      left join ' . DB_PROJECT . '.groups g on ga.group_id = g.group_id
      left join ' . DB_PROJECT . '.activities a on ga.activity_id = a.activity_id
      where
        ' . $field . ' = ?
    ';
    $params = [$group_id];
  
    return $this->db->pl_query_prepared( $sql, $params, true );
  }
}