<?php

class Participants
{
  private Model $db;

  public function __construct()
  {
    $this->db = new Model();
  }

  /**
   * Obtiene todas las filas de la tabla `participants`.
   * 
   * @return array Lista de participantes o un array vacío si no hay resultados.
   */
  public function GetAll( string $where = '' ): array
  {
    // Filtrado
    if( $where > '' )
      $where = ' where participant_name like "%' . $where . '%"';
  
    $sql = 'select * from ' . DB_PROJECT . '.participants' . $where;
    return $this->db->pl_query_prepared( $sql, [], true );
  }

  /**
   * Obtiene todas las filas de la tabla `participants` asociadas a un usuario.
   * 
   * @param int $user_id ID del usuario.
   * @return array Lista de participantes o un array vacío si no hay resultados.
   */
  public function GetRows( int $user_id, string $where = '' ): array
  {
    // Filtrado
    if( $where > '' )
      $where = ' and p.participant_name like "%' . $where . '%"';

    $sql = '
      select
        p.*, gp.*
      from ' . DB_PROJECT . '.participants p
      left join ' . DB_PROJECT . '.group_participants gp on p.participant_id = gp.participant_id
      where
        p.user_id = ?
      ' .  $where . '
    ';
    $params = [$user_id];
  
    return $this->db->pl_query_prepared( $sql, $params, true );
  }

  /**
   * Obtiene una fila específica de la tabla `participants` según `participant_id` o `participant_id2`.
   * 
   * @param int|string $participant_id Identificador del participante (ID numérico o ID alfanumérico).
   * @return array Datos del participante si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int|string $participant_id ): array
  {
    $field = is_numeric( $participant_id ) ? 'participant_id' : 'participant_id2';
  
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.participants 
      where
        ' . $field . ' = ?
    ';
    $params = [$participant_id];
  
    return $this->db->pl_query_prepared( $sql, $params, true );
  }

  /**
   * Obtiene el `user_id` de la tabla `users` asociado a un participante según `id2`.
   * 
   * @param string $participant_id2 Identificador alfanumérico del participante.
   * @return array Datos del usuario si existe, o un array vacío si no hay resultados.
   */
  public function GetUserIdByParticipant( string $participant_id2 ): array
  {
    $sql = '
      select
        u.user_id
      from ' . DB_PROJECT . '.participants p
      left join ' . DB_PROJECT . '.users u on p.user_id = u.user_id
      where
        p.participant_id2 = ?
    ';
    $params = [$participant_id2];

    return $this->db->pl_query_prepared( $sql, $params, true );
  }
}