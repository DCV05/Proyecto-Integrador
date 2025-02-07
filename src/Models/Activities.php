<?php

class Activities
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
  }

  /**
   * Obtiene todas las filas de la tabla `activities`.
   * 
   * @return array Lista de actividades o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.activities
    ';
    return $this->db->pl_query( $sql, true );
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
        activity_id2 = "' . $this->db->esc( $activity_id2 ) . '"
    ';
    $this->db->pl_query( $sql );

    // Devolvemos el array de datos
    return $this->db->next_row()
      ? $this->db->get_row()
      : []
    ;
  }

  /**
   * Obtiene la actividad asociada a un participante específico.
   * 
   * @param int $participant_id ID del participante.
   * @return array Datos de la actividad si existe, o un array vacío si no hay resultados.
   */
  public function GetParticipantLinkedRows( int $participant_id ): array
  {
    $sql = '
      select
        a.*
      from ' . DB_PROJECT . '.activities a
      left join ' . DB_PROJECT . '.activities_participants ap on a.activity_id = ap.activity_id
      where
        ap.participant_id = ' . $this->db->esc( $participant_id );
    $this->db->pl_query( $sql );

    // Devolvemos el array de datos
    return $this->db->next_row()
      ? $this->db->get_row()
      : []
    ;
  }
}