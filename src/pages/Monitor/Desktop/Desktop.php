<?php

class MonitorDesktopController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    app_restrict();

    global $participants, $events, $filtered_activities;

    $mod_schedules          = new SchedulesParticipants();
    $mod_activities         = new Activities();
    $mod_groups             = new Groups();
    $mod_groups_participants = new GroupParticipants();

    // Capturamos el grupo del monitor
    $group = $mod_groups->GetRow( $_SESSION['app']['user']['user_id'] );
    if( empty( $group[0] ) )
      return;

    // Capturamos los participantes relacionados
    $group_id = $group[0]['group_id'];
    $participants = $mod_groups_participants->GetRow( $group_id );

    // -------------------------------------------------------------------------------------------
    // Captura de actividades
    // -------------------------------------------------------------------------------------------

    $events = $activities = [];
    foreach( $participants as $participant )
    {
      if( !empty( $participant['group_id'] ) )
      {
        // Capturamos las actividades de cada participante
        $participant_activities = $mod_activities->GetGroupLinkedRows( $participant['group_id'] );
        $activities = [...$activities, ...$participant_activities];
      }

      // Capturamos los eventos de cada participante
      $participant_events = $mod_schedules->GetEvents( $participant['participant_id2'] );
      $events = [...$events, ...$participant_events];
    }

    // -------------------------------------------------------------------------------------------
    // Filtrado
    // -------------------------------------------------------------------------------------------

    // Filtramos las actividades a aquellas que estén en el tramo en el que el participante esté en el campamento
    $activities_id2 = [];
    $filtered_activities = array_filter( $activities, function( $activity ) use( $events, &$activities_id2 ): bool {

      // Pasamos el datetime a fecha
      $activity_date = date( 'Y-m-d', strtotime( $activity['activity_datetime_start'] ) );

      // Iteramos sobre cada evento y comprobamos si la actividad cae en alguno de los rangos
      foreach( $events as $event )
      {
        if( $activity_date <= $event['end_day'] && !in_array( $activity['activity_id2'], $activities_id2 ) )
        {
          array_push( $activities_id2, $activity['activity_id2'] );
          return true; // La actividad está dentro de este intervalo
        }
      }
      
      return false; // Ningún evento incluye la fecha de la actividad
    } );

    return;
  }

  public function calendar_events(): string
  {
    global $participants, $events, $filtered_activities;
    $value = '';

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
        if( $start_timestamp <= $event['end_day'] ) {

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

  public function content(): string 
  {
    global $participants, $events, $filtered_activities, $colors;
    $value = '';
    
    $widget_activities = $widget_reminders = [];
    foreach( $filtered_activities as $activity_id => $activity )
    {
      $activity_color = $colors[$activity_id] ?? 'blue';

      // Formateamos los campos de la actividad
      $activity_datetime = sprintf( 
          '%s - %s'
        , date( 'd/m/y | H:i', strtotime( $activity['activity_datetime_start'] ) )
        , date( 'H:i', strtotime( $activity['activity_datetime_end'] ) )
      );

      $widget_activities[] = [
          'time'  => $activity_datetime
        , 'title' => $activity['activity_name_' . DEF_LANG]
        , 'color' => 'bg-' . $activity_color . '-100'
      ];
    }

    $today = date( 'Y-m-d' );
    $calendar_widget = new CalendarWidget( $widget_activities, $widget_reminders, $today );
    $value = $calendar_widget->render();
    
    return $value;
  }
}

?>