<?php declare( strict_types = 1 );

class ActivitiesMonitors
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
  }

  /**
   * Obtiene todas las filas de la tabla `activities_monitors`.
   * 
   * @return array Lista de relaciones entre actividades y monitores o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.activities_monitors
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene una fila específica de la tabla `activities_monitors` según `activity_id` y `monitor_id`.
   * 
   * @param int $activity_id ID de la actividad.
   * @param int $monitor_id ID del monitor.
   * @return array Datos de la relación si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int $activity_id, int $monitor_id ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.activities_monitors
      where
        activity_id = "' . $this->db->esc( $activity_id ) . '" and
        monitor_id = "' . $this->db->esc( $monitor_id ) . '"
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene una fila específica de la tabla `activities_monitors` según `monitor_id`.
   * 
   * @param int $monitor_id ID del monitor.
   * @return array Datos de la relación si existe, o un array vacío si no hay resultados.
   */
  public function GetMonitorRows( int $monitor_id ): array
  {
    $sql = '
      select
          am.* 
        , a.*
      from ' . DB_PROJECT . '.activities_monitors am
      left join ' . DB_PROJECT . '.activities a on am.activity_id = a.activity_id
      where
        am.monitor_id = "' . $this->db->esc( $monitor_id ) . '"
    ';
    return $this->db->pl_query( $sql, true );
  }
}