<?php declare( strict_types = 1 );

class Users
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
  }

  /**
   * Obtiene todas las filas de la tabla `users`.
   * 
   * @return array Lista de usuarios o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.users
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene una fila específica de la tabla `users` según `user_id2`.
   * 
   * @param string $user_id2 Identificador del usuario.
   * @return array Datos del usuario si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( string $user_id2 ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.users
      where
        user_id2 = "' . $this->db->esc( $user_id2 ) . '"
    ';
    $this->db->pl_query( $sql );

    // Devolvemos el array de datos
    return $this->db->next_row()
      ? $this->db->get_row()
      : []
    ;
  }
}