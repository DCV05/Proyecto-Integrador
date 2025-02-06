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
  public function GetRow( int $activity_id, int $participant_id = null ): array
  {
    // Si hemos puesto un filtro por participante, lo añadimos a la consulta
    $where = !is_null( $participant_id )
    ? 'and participant_id = ' . $participant_id
    : '';
    
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.activities_participants
      where
        activity_id = "' . $this->db->esc( $activity_id ) . '"
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
  public function GetActivityDetails( int $activity_id, int $participant_id = null ): array
  {
    // Si hemos puesto un filtro por participante, lo añadimos a la consulta
    $where = !is_null( $participant_id )
    ? 'and participant_id = ' . $participant_id
    : '';
    
    $sql = '
      select 
          ap.*
        , p.participant_id2
        , p.participant_name
        , p.participant_allergies
        , p.participant_birth_date
        , p.participant_special_needs
      from ' . DB_PROJECT . '.activities_participants ap
      join ' . DB_PROJECT . '.participants p on ap.participant_id = p.participant_id
      where
        ap.activity_id = "' . $this->db->esc( $activity_id ) . '"
        ' . $where . '
    ';

    return $this->db->pl_query( $sql, true );
  }
}