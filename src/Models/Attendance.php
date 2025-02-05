<?php

class Attendance
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
  }

  /**
   * Obtiene todas las filas de la tabla `attendance`.
   * 
   * @return array Lista de registros de asistencia o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.attendance
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene una fila específica de la tabla `attendance` según `attendance_id2`.
   * 
   * @param string $attendance_id2 Identificador de la asistencia.
   * @return array Datos de la asistencia si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( string $attendance_id2 ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.attendance
      where
        attendance_id2 = "' . $this->db->esc( $attendance_id2 ) . '"
    ';
    $this->db->pl_query( $sql );

    return $this->db->next_row()
      ? $this->db->get_row()
      : []
    ;
  }
}