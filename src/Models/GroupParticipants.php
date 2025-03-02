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
   * Obtiene una fila específica de la tabla `group_participants` según `group_id` o `group_id2`.
   * 
   * @param int|string $group_id Identificador del grupo (ID numérico o ID alfanumérico).
   * @return array Datos del grupo si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int|string $group_id, string $where = '' ): array
  {
    $field = is_numeric( $group_id ) ? 'g.group_id' : 'g.group_id2';

    $sql = '
      select
          gp.* 
        , p.*
        , g.group_id
      from ' . DB_PROJECT . '.group_participants gp
      left join ' . DB_PROJECT . '.groups g on gp.group_id = g.group_id
      left join ' . DB_PROJECT . '.participants p on gp.participant_id = p.participant_id
      where
        ' . $field . ' = ?
        ' . $where . '
    ';
    $params = [$group_id];
  
    return $this->db->pl_query_prepared( $sql, $params, true, true );
  }

  /**
   * Obtiene una fila específica de la tabla `group_participants` según `participant_id` o `participant_id2`.
   * 
   * @param int|string $participant_id Identificador del participante (ID numérico o ID alfanumérico).
   * @return array Datos del participante si existe, o un array vacío si no hay resultados.
   */
  public function GetGroupPID( int|string $participant_id, string $where = '' ): array
  {
    $field = is_numeric( $participant_id ) ? 'p.participant_id' : 'p.participant_id2';

    $sql = '
      select
          gp.* 
        , p.*
        , g.participant_id
      from ' . DB_PROJECT . '.group_participants gp
      left join ' . DB_PROJECT . '.participants p on gp.participant_id = p.participant_id
      where
        ' . $field . ' = ?
        ' . $where . '
    ';
    $params = [$participant_id];
  
    return $this->db->pl_query_prepared( $sql, $params, true, true );
  }
}