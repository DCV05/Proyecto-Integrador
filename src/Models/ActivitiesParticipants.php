<?php

class ActivitiesParticipants
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
  }

  /**
   * Obtiene todas las filas de la tabla `activities_participants`.
   * 
   * @return array Lista de relaciones entre actividades y participantes o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.activities_participants
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene una fila específica de la tabla `activities_participants` según `activity_id` y `participant_id`.
   * 
   * @param int $activity_id ID de la actividad.
   * @param int $participant_id ID del participante.
   * @return array Datos de la relación si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int $activity_id, int $participant_id ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.activities_participants
      where
        activity_id = "' . $this->db->esc( $activity_id ) . '" and
        participant_id = "' . $this->db->esc( $participant_id ) . '"
    ';
    return $this->db->pl_query( $sql, true );
  }
}