<?php

class Users
{
  private Model $db;

  public function __construct()
  {
    $this->db = new Model();
  }

  /**
   * Obtiene todas las filas de la tabla `users`.
   * 
   * @return array Lista de usuarios o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = 'select * from ' . DB_PROJECT . '.users';
    return $this->db->pl_query_prepared( $sql, [], true );
  }

  /**
   * Obtiene una fila específica de la tabla `users` según `user_id2`.
   * 
   * @param string|int $user_id Identificador del usuario.
   * @return array Datos del usuario si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( string|int $user_id ): array
  {
    $field = is_numeric( $user_id ) ? 'user_id' : 'user_id2';

    $sql = '
      select
        user_id, user_id2, user_email, role, enabled , has_schedule
      from ' . DB_PROJECT . '.users 
      where
        ' . $field . ' = ?
    ';
    $params = [$user_id];
  
    return $this->db->pl_query_prepared( $sql, $params, true );
  }
}