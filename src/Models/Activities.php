<?php

class Activities
{
  private Model $db;

  public function __construct()
  {
    $this->db = new Model();
  }

  /**
   * Obtiene todas las filas de la tabla `activities`.
   * 
   * @return array Lista de actividades o un array vacío si no hay resultados.
   */
  public function GetRows( string $where = '' ): array
  {
    // Filtrado
    if( $where > '' )
      $where = ' where activity_name_es like "%' . $where . '%" or activity_name_en like "%' . $where . '%"';

    $sql = 'select * from ' . DB_PROJECT . '.activities ' . $where;
    return $this->db->pl_query_prepared( $sql, [], true );
  }

  /**
   * Obtiene una fila específica de la tabla `activities` según `activity_id2`.
   * 
   * @param string $activity_id2 Identificador de la actividad.
   * @return array Datos de la actividad si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( string $activity_id2 ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.activities
      where
        activity_id2 = ?
    ';
    $params = [$this->db->esc( $activity_id2 )];

    return $this->db->pl_query_prepared( $sql, $params, true );
  }

  /**
   * Obtiene la actividad asociada a un grupo específico.
   * 
   * @param int $group_id ID del grupo.
   * @return array Datos de la actividad si existe, o un array vacío si no hay resultados.
   */
  public function GetGroupLinkedRows( int $group_id ): array
  {
    $sql = '
      select
        a.*
      from ' . DB_PROJECT . '.activities a
      left join ' . DB_PROJECT . '.group_activities ag on a.activity_id = ag.activity_id
      where
        ag.group_id = ?';
    $params = [$group_id];
    
    return $this->db->pl_query_prepared( $sql, $params, true );
  }
}