<?php

class Attendance
{
  private Model $db;

  public function __construct()
  {
    $this->db = new Model();
  }

  /**
   * Obtiene todas las filas de la tabla `attendance`.
   * 
   * @return array Lista de registros de asistencia o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = 'select * from ' . DB_PROJECT . '.attendance';
    return $this->db->pl_query_prepared( $sql, [], true );
  }

  /**
   * Obtiene una fila específica de la tabla `attendance` según `activity_id`.
   * 
   * @param string $activity_id Identificador de la asistencia.
   * @return array Datos de la asistencia si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int $activity_id ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.attendance
      where
        activity_id = ?
    ';
    $params = [$activity_id];
    
    return $this->db->pl_query_prepared( $sql, $params, true );
  }

  /**
   * Obtiene una fila específica de la tabla `attendance` según `activity_id`.
   * 
   * @param int $group_id Identificador del grupo.
   * @param int $activity_id Identificador de la asistencia.
   * @return array Datos de la asistencia si existe, o un array vacío si no hay resultados.
   */
  public function GetGroupRows( int $group_id, int $activity_id ): array
  {
    // Unimos las tablas con left join para que, si el registro en attendance no existe, aparezca pero con NULL
    // Esto es necesario para poder imprimir la tabla de asistencias en una actividad concreta aunque no exista el registro en `attendance`
    $sql = '
      select
          a.*
        , p.*
      from ' . DB_PROJECT . '.group_participants gp
      left join ' . DB_PROJECT . '.participants p on gp.participant_id = p.participant_id
      left join ' . DB_PROJECT . '.attendance a on gp.participant_id = a.participant_id and a.activity_id = ?
      where
        gp.group_id = ? 
    ';
    $params = [$activity_id, $group_id];
    return $this->db->pl_query_prepared( $sql, $params, true );
  }

  /**
   * Obtiene una fila específica de la tabla `attendance` según `activity_id`.
   * 
   * @param string $activity_id Identificador de la asistencia.
   * @return array Datos de la asistencia si existe, o un array vacío si no hay resultados.
   */
  public function GetParticipantRow( int $participant_id, int $activity_id ): array
  {
    $sql = '
      select
        *
      from ' . DB_PROJECT . '.attendance
      where
        participant_id = ? and
        activity_id = ?
    ';
    $params = [$participant_id, $activity_id];
    return $this->db->pl_query_prepared( $sql, $params, true );
  }
}