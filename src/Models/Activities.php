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
}