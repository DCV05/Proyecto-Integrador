<?php

class Participants
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
  }

  /**
   * Obtiene todas las filas de la tabla `participants`.
   * 
   * @return array Lista de participantes o un array vacío si no hay resultados.
   */
  public function GetAll(): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.participants
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene todas las filas de la tabla `participants` asociadas a un usuario.
   * 
   * @param int $user_id ID del usuario.
   * @return array Lista de participantes o un array vacío si no hay resultados.
   */
  public function GetRows( int $user_id ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.participants 
      where
        user_id = "' . $this->db->esc( $user_id ) . '"
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene una fila específica de la tabla `participants` según `participant_id` o `participant_id2`.
   * 
   * @param int|string $participant_id Identificador del participante (ID numérico o ID alfanumérico).
   * @return array Datos del participante si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( int|string $participant_id ): array
  {
    // Determinamos si el identificador es numérico o un string
    $field = is_numeric( $participant_id ) ? 'participant_id' : 'participant_id2';

    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.participants 
      where
        ' . $field . ' = "' . $this->db->esc( $participant_id ) . '"
    ';
    return $this->db->pl_query( $sql, true );
  }
}