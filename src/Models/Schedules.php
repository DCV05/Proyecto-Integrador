<?php

class Schedules
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
  }

  /**
   * Obtiene todas las filas de la tabla `schedules`.
   * 
   * @return array Lista de horarios o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.schedules
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene una fila específica de la tabla `schedules` según `schedule_id2`.
   * 
   * @param string $schedule_id2 Identificador del horario.
   * @return array Datos del horario si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( string $schedule_id2 ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.schedules
      where
        schedule_id2 = "' . $this->db->esc( $schedule_id2 ) . '"
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene los eventos de un participante en formato JSON para un calendario.
   * 
   * @param string $participant_id ID del participante.
   * @return array Array de eventos.
   */
  public function GetEvents( string $participant_id2 ): array
  {
    $db     = new pl_model();
    $value  = '';

    // Buscamos los horarios relacionados al participante
    $sql = '
      select
        s.*
      from ' . DB_PROJECT . '.schedule s
      left join ' . DB_PROJECT . '.participants p on s.participant_id = p.participant_id
      where
        p.participant_id2 = "' . $db->esc( $participant_id2 ) . '"
    ';
    return $this->db->pl_query( $sql, true );
  }
}