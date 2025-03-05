<?php

/**
 * Clase para manejar las fechas del calendario.
 */
class CalendarDate
{
  public string $day;
  public string $day_of_week;
  public string $month;
  public string $year;
  public int $days_in_month;
  public int $first_day_of_month;

  /**
   * Constructor del objeto de fecha.
   * 
   * @param string $date Fecha en formato YYYY-MM-DD.
   */
  public function __construct( string $date )
  {
    $timestamp = strtotime( $date );

    $this->day                 = date( 'j', $timestamp );
    $this->day_of_week         = date( 'l', $timestamp );
    $this->month               = date( 'F', $timestamp );
    $this->year                = date( 'Y', $timestamp );
    $this->days_in_month       = date( 't', $timestamp );
    $this->first_day_of_month  = date( 'N', strtotime( $this->year . '-' . $this->month . '-01' ) );
  }
}

/**
 * Componente CalendarWidget - Genera un calendario con eventos y recordatorios.
 */
class CalendarWidget
{
  private array $events;
  private array $reminders;
  private CalendarDate $calendar_date;

  /**
   * Constructor del calendario.
   * 
   * @param array $events    Lista de eventos con ['time' => 'HH:MM AM/PM', 'title' => 'Evento', 'color' => 'bg-color'].
   * @param array $reminders Lista de recordatorios con ['title' => 'Recordatorio', 'color' => 'bg-color'].
   * @param string $date     Fecha en formato YYYY-MM-DD (opcional, por defecto usa la actual).
   */
  public function __construct( array $events = [], array $reminders = [], string $date = '' )
  {
    $this->events        = $events;
    $this->reminders     = $reminders;
    $this->calendar_date = new CalendarDate( $date ?: date( 'Y-m-d' ) );
  }

  /**
   * Genera la estructura del calendario.
   * 
   * @return string HTML del calendario.
   */
  private function render_calendar(): string
  {
    $calendar_days = '';

    // Espacios vacíos antes del primer día del mes
    for( $index = 1; $index < $this->calendar_date->first_day_of_month; $index++ )
      $calendar_days .= '<div></div>';

    // Generar los días del mes (ahora con `for` en vez de `foreach`)
    for( $index = 1; $index <= $this->calendar_date->days_in_month; $index++ )
    {
      $is_today = '';
      if( $index == $this->calendar_date->day )
        $is_today = 'bg-red-500 text-white rounded-full';

      $calendar_days .= '<div class="flex items-center justify-center py-1.5 text-sm '. $is_today . '">' . $index . '</div>';
    }

    $week_days = DEF_LANG == 'en'
      ? ['S', 'M', 'T', 'W', 'T', 'F', 'S']
      : ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
    foreach( $week_days as $day_id => $day )
      $week_days[$day_id] = '<span class="text-gray-500">' . $day . '</span>';

    return '
      <div class="bg-white shadow rounded-2xl p-4 w-72">
        <div class="text-2xl font-semibold text-red-500 text-center pt-4">' . $this->calendar_date->day_of_week . '</div>
        <div class="text-8xl font-bold text-center pb-8">' . $this->calendar_date->day . '</div>
        <hr class="py-4">
        <div class="text-gray-500 text-lg ml-2.5">' . $this->calendar_date->month . '</div>
        <div class="grid grid-cols-7 gap-1 text-center text-gray-600 mt-3">
          ' . implode( $week_days ) . '
          ' . $calendar_days        . '
        </div>
      </div>
    ';
  }

  /**
   * Genera la lista de eventos.
   * 
   * @return string HTML de los eventos.
   */
  private function render_events(): string
  {
    if( empty( $this->events ) )
      return '<p class="text-gray-500">' . pl_label( 'no_events_today' ) . '</p>';

    $events_html = '
      <div class="text-md font-bold mb-2 flex items-center gap-1">
        <i class="icon">calendar_today</i>
        ' . count( $this->events ) . ' ' . pl_label( 'events' ) . '
      </div>
    ';

    foreach( $this->events as $event )
    {
      $events_html .= '
        <div class="p-2 mb-2 rounded-lg text-sm font-semibold ' . $event['color'] . '">
          <span class="text-xs text-gray-600">' . $event['time'] . '</span>
          <div>' . $event['title'] . '</div>
        </div>
      ';
    }

    return '<div class="bg-white shadow rounded-2xl p-4 w-48">' . $events_html . '</div>';
  }

  /**
   * Genera la lista de recordatorios.
   * 
   * @return string HTML de los recordatorios.
   */
  private function render_reminders(): string
  {
    if( empty( $this->reminders ) )
      return '<p class="text-gray-500">' . pl_label( 'no_reminders_today' ) . '</p>';

    $reminders_html = '
      <div class="text-md font-bold mb-2 flex items-center gap-1">
        <i class="icon">checklist</i>
        ' . count( $this->reminders ) . ' ' . pl_label( 'reminders' ) . '
      </div>
    ';
    
    foreach( $this->reminders as $reminder )
    {
      $reminders_html .= '
        <div class="flex items-center gap-2 p-1">
          <div class="w-2 h-2 rounded-full ' . $reminder['color'] . '"></div>
          <span class="text-sm">' . $reminder['title'] . '</span>
        </div>
      ';
    }

    return '<div class="bg-white shadow rounded-2xl p-4 w-48">' . $reminders_html . '</div>';
  }

  /**
   * Renderiza el componente del calendario con eventos y recordatorios.
   * 
   * @return string HTML del componente completo.
   */
  public function render(): string
  {
    return '
    <div class="flex gap-4 rounded-3xl bg-gray-50 p-4 w-full lg:w-fit">
      ' . $this->render_calendar() . '
      <div class="flex flex-col gap-4">
        ' . $this->render_events() . '
        ' . $this->render_reminders() . '
      </div>
    </div>';
  }
}

?>