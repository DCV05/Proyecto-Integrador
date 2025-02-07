<?php

class BlogPosts
{
  private pl_model $db;

  public function __construct()
  {
    $this->db = new pl_model();
  }

  /**
   * Obtiene todas las filas de la tabla `posts`.
   * 
   * @return array Lista de publicaciones o un array vacío si no hay resultados.
   */
  public function GetRows(): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.posts
    ';
    return $this->db->pl_query( $sql, true );
  }

  /**
   * Obtiene una fila específica de la tabla `posts` según `post_id2`.
   * 
   * @param string $post_id2 Identificador de la publicación.
   * @return array Datos de la publicación si existe, o un array vacío si no hay resultados.
   */
  public function GetRow( string $post_id2 ): array
  {
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.posts
      where
        post_id2 = "' . $this->db->esc( $post_id2 ) . '"
    ';
    return $this->db->pl_query( $sql, true );
  }
}