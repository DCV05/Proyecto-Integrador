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
   * Obtiene una fila específica de la tabla `attendance` según `activity_id`.
   * 
   * @param string $activity_id Identificador de la asistencia.
   * @return array Datos de la asistencia si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int $activity_id, int $participant_id = null ): array
  {
    // Si hemos puesto un filtro por participante, lo añadimos a la consulta
    $where = !is_null( $participant_id )
      ? 'and participant_id = ' . $participant_id
      : '';

    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.attendance
      where
        activity_id = ' . $this->db->esc( $activity_id ) . '
        ' . $where . '
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene la lista de participantes de una actividad junto con sus detalles de usuario.
   * 
   * @param int $activity_id ID de la actividad.
   * @return array Lista de participantes con detalles de usuario.
   */
  public function GetAttendanceDetails( int $activity_id, int $participant_id = null ): array
  {
    $mod_participants = new Participants();

    // Obtenemos la lista de registros de asistencia
    $attendance_list = $this->GetRow( $activity_id, $participant_id );
    if( empty( $attendance_list ) )
      return [];

    $detailed_list = [];

    // Recorremos cada registro de asistencia y obtenemos los datos del participante
    foreach( $attendance_list as $attendance )
    {
      $participant_details = $mod_participants->GetRow( $attendance['participant_id'] );
      $detailed_list[] = [
          'attendance'  => $attendance
        , 'participant' => $participant_details
      ];
    }

    return $detailed_list;
  }
}