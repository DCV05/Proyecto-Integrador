<?php

class MonitorScheduleController
{
  public string $monitor_id2;

  public function index(): void
  {
    // Control de seguridad
    app_security();
    app_restrict();

    // Capturamos el id2 del monitor
    $this->monitor_id2 = $_SESSION['app']['user']['user_id2'];
    return;
  }

  public function events(): string
  {
    $value          = '';
    $mod_schedules  = new SchedulesMonitors();

    // Capturamos los eventos
    $events = $mod_schedules->GetEvents( $this->monitor_id2 );

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