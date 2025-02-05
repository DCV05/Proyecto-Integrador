<?php

class Participants
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
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
   * Obtiene una fila específica de la tabla `participants` según `participant_id2`.
   * 
   * @param string $participant_id2 Identificador del participante.
   * @return array Datos del participante si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( string $participant_id2, int $user_id ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.participants 
      where
        participant_id2 = "' . $this->db->esc( $participant_id2 ) . '" and
        user_id = "' . $this->db->esc( $user_id ) . '"
    ';
    $this->db->pl_query( $sql );

    // Devolvemos el array de datos
    return $this->db->next_row()
      ? $this->db->get_row()
      : []
    ;
  }
}