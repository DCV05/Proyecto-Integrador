<?php

class ScheduleController
{
  public string|null $participant_id2;
  public array $participant;

  public function index(): void
  {
    $db = new pl_model();

    // Capturamos el id2 del participante
    $this->participant_id2 = pl_get( 'pid2', null );
    if( !$this->participant_id2 )
      pl_redirect( '/account' );

    // Comprobamos que este participante esté vinculado al usuario de la sesión
    $sql = '
      select
        *
      from ' . DB_PROJECT . '.participants
      where
        participant_id2 = "' . $this->participant_id2 . '" and
        user_id = ' . $_SESSION['app']['user']['user_id'];
    $db->pl_query( $sql );

    // Si no hay participantes vinculados a este usuario con los parámetros insertados, redirigimos
    if( !$db->next_row() )
      pl_redirect( '/account' );
    else
      $this->participant = $db->get_row();

    return;
  }

  public function events(): string
  {
    $value = '';
    $db    = new pl_model();

    // Buscamos las cuentas relacionadas al usuario
    $sql = '
      select
        s.*
      from ' . DB_PROJECT . '.schedule s
      left join ' . DB_PROJECT . '.participants p on s.participant_id = p.participant_id
      where
        p.participant_id = ' . $this->participant['participant_id'];
    $events = $db->pl_query( $sql, true );

    // Compilamos los items de JS
    $js_items = '';
    foreach( $events as $event_id => $event )
    {
      // Generamos el JS
      $js_items .= '
        {
          id:     "' . $event_id + 1 . '",
          title:  "' . $event['start_day'] . ' - ' . $event['end_day'] . '",
          start:  "' . $event['start_day'] . '",
          end:    "' . $event['end_day'] . '"
        },
      ';    
    }

    // Eliminamos el último '/'
    $js_items = rtrim( $js_items, ',' );

    // Encapsulamos
    $value = '
      const events = [
        ' . $js_items . '
      ]
    ';
    
    return $value;
  }
}

?>