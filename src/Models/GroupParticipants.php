<?php

class GroupParticipants
{
  private Model $db;

  public function __construct()
  {
    $this->db = new Model();
  }

  /**
   * Obtiene todas las filas de la tabla `group_participants`.
   * 
   * @return array Lista de actividades o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = 'select * from ' . DB_PROJECT . '.group_participants';
    return $this->db->pl_query_prepared( $sql, [], true );
  }

  /**
   * Obtiene una fila específica de la tabla `group_participants` según `monitor_id`.
   * 
   * @param int $monitor_id Identificador de la actividad.
   * @return array Datos de la actividad si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int $group_id ): array
  {
    $sql = '
      select
          gp.* 
        , p.*
      from ' . DB_PROJECT . '.group_participants gp
      left join ' . DB_PROJECT . '.participants p on gp.participant_id = p.participant_id
      where
        gp.group_id = ?
    ';
    $params = [$this->db->esc( $group_id )];
    return $this->db->pl_query_prepared( $sql, $params, true );
  }
}