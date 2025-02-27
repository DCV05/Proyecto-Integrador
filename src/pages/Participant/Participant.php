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
      pl_redirect( '/tutor/account' );

    // Buscamos en la DB el participante. En caso de que no exista, redirigimos al account
    $this->participant = $mod_participants->GetRow( $this->participant_id2 );
    if( empty( $this->participant ) || ( $_SESSION['app']['user']['user_id'] != $this->participant[0]['user_id'] && $_SESSION['app']['user']['role'] == 0 ) )
      pl_redirect( '/tutor/account' );

    return;
  }

  public function events(): string
  {
    $value          = '';
    $mod_schedules  = new SchedulesParticipants();
    $mod_activities = new Activities();

    // Capturamos los eventos
    $activities = $mod_activities->GetRows();
    $events     = $mod_schedules->GetEvents( $this->participant_id2 );

    // Filtramos las actividades a aquellas que estén en el tramo en el que el participante esté en el campamento
    $filtered_activities = array_filter( $activities, function( $activity ) use ( $events ): bool {
      // Pasamos el datetime a fecha
      $activity_date = date( 'Y-m-d', strtotime( $activity['activity_datetime_start'] ) );

      // Iteramos sobre cada evento y comprobamos si la actividad cae en alguno de los rangos
      foreach( $events as $event ) {
        if( $activity_date >= $event['start_day'] && $activity_date <= $event['end_day'] )
          return true; // La actividad está dentro de este intervalo
      }
      
      return false; // Ningún evento incluye la fecha de la actividad
    } );

    // Compilamos los items de JS
    $js_items = '';
    foreach( $filtered_activities as $activity_id => $activity )
    {
      // Inicializamos las variables para la fecha y hora
      $start_timestamp = date( 'Y-m-d', strtotime( $activity['activity_datetime_start'] ) );
      $end_timestamp   = date( 'Y-m-d', strtotime( $activity['activity_datetime_end'] ) );
      
      // Iteramos sobre cada evento para obtener la hora exacta, si está definida
      foreach( $events as $event )
      {
        // Si el evento está dentro de la fecha de los eventos del participante, lo mostramos
        if( $start_timestamp >= $event['start_day'] && $start_timestamp <= $event['end_day'] ) {

          // Concatenamos la hora al formato de la fecha
          $start_datetime = !empty( $event['start_time'] )
            ? $start_timestamp . 'T' . $event['start_time']
            : $activity['activity_datetime_start'];

          $end_datetime = !empty( $event['end_time'] )
            ? $end_timestamp . 'T' . $event['end_time']
            : $activity['activity_datetime_end'];
          
          // Cerramos el bucle
          break;
        }
      }

      // Añadimos el evento
      $title = $activity['activity_name_' . DEF_LANG] . ' | ' . $event['start_day'] . ' - ' . $event['end_day'];
      $js_items .= '
        {
            id:     "' . ( $activity_id + 1 )                     . '"
          , title:  "' . $title                                   . '"
          , start:  "' . $start_datetime                          . '"
          , end:    "' . $end_datetime                            . '"
          , url:    "/activity?aid2=' . $activity['activity_id2'] . '"
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