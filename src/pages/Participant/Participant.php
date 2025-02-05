<?php

class ParticipantController
{
  public string|null $participant_id2;
  public array $participant;

  public function index(): void
  {
    $mod_participants = new Participants();

    // Capturamos el id2 del participante
    $this->participant_id2 = pl_get( 'pid2', null );
    if( !$this->participant_id2 )
      pl_redirect( '/account' );

    // Buscamos en la DB el participante. En caso de que no exista, redirigimos al account
    $this->participant = $mod_participants->GetRow( $this->participant_id2, $_SESSION['app']['user']['user_id'] );
    if( empty( $this->participant ) )
      pl_redirect( '/account' );

    return;
  }

  public function events(): string
  {
    $value          = '';
    $mod_schedules  = new Schedules();

    // Capturamos los eventos
    $events = $mod_schedules->GetEvents( $this->participant_id2 );

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