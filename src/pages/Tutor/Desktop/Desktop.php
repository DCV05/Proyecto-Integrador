<?php

class TutorDesktopController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    app_restrict();

    global $participants, $events, $filtered_activities;

    $mod_participants = new Participants();
    $mod_schedules    = new SchedulesParticipants();
    $mod_activities   = new Activities();

    // Capturamos los participantes
    $participants = $mod_participants->GetRows( $_SESSION['app']['user']['user_id'] );

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

  /**
   * Genera el calendario para cada participante del usuario.
   *
   * @global int $role Rol del usuario en sesión.
   * @return string Código HTML con el modal y scripts.
   */
  public function calendars(): string
  {
    global $role;
    $value      = '';
    $mod_users  = new Users();

    // Buscamos en la DB si el usuario debe rellenar el calendario
    $user = $mod_users->GetRow( $_SESSION['app']['user']['user_id'] )[0];
    if( $role == 0 && $user['has_schedule'] == 0 )
    {
      $mod_participants = new Participants();

      // Capturamos los participantes del usuario
      $participants = $mod_participants->GetRows( $_SESSION['app']['user']['user_id'] );
      $calendars    = '';
      $aside        = '';
      $calendar_ids = [];

      foreach( $participants as $participant_id => $participant )
      {
        $participant_id2 = $participant['participant_id2'];
        $hidden = $participant_id == 0 ? '' : '!hidden';
        $bg     = $participant_id == 0 ? 'bg-gray-100' : '';

        // Añadir participante al sidebar
        $aside .= '
          <div
            class="' . $bg . ' cursor-pointer participant-selector grid grid-cols-[auto_1fr] gap-2 px-[0.7rem] py-2 items-start hover:bg-gray-100 transform transition duration-300 mr-2 rounded-lg font-bold"
            data-target="calendar-' . $participant_id2 . '">
            <div class="w-9 h-9 bg-cyan-500 shadow-landing white-svg text-i-2xl tree-icon-container">
              <i class="text-black text-3xl icon">person</i>
            </div>
            <p class="text-base mt-1">' . $participant['participant_name'] . '</p>
          </div>
        ';

        // Añadir calendario
        $calendars      .= '<div id="calendar-' . $participant_id2 . '" class="calendar-container ' . $hidden . ' h-[28rem]"></div>';
        $calendar_ids[] = '#calendar-' . $participant_id2;
      }

      $content = '
        <h3 class="text-md font-normal max-w-4xl mb-8">' . pl_label( 'fill_participant_schedule_description' ) . '</h3>
        <form id="calendar-form" class="flex flex-col gap-2">
          <div class="grid grid-cols-5 gap-3">
            <div class="col-span-1 flex flex-col">
              <div class="overflow-y-auto space-y-2">
                ' . $aside . '
              </div>
            </div>
            <div class="col-span-4 p-mini-block">
              ' . $calendars . '
            </div>
          </div>

          <div class="flex justify-end">
            <button type="submit" class="p-button">
              <i class="icon">send</i>
              <span>' . pl_label( 'send-button' ) . '</span>
            </button>
          </div>
        </form>
      ';

      // Generamos el modal
      $elements = app_generate_modal( pl_label( 'fill_participant_schedule_title' ), $content, 'max-w-7xl' );
      
      // Script para manejar la visualización de calendarios y forzar la carga
      $value = '
        <script>
          $( document ).ready( function() {
            var modal_elements = ' . json_encode( $elements ) . ';
            pl_dom( modal_elements );
  
            window.CALENDAR_IDS = ' . json_encode( $calendar_ids ) . ';
  
            // Renderizar todos los calendarios y guardarlos en calendar_instances
            window.CALENDAR_IDS.forEach( id => {
              render_calendar( $( id )[0] );
            });
          });
        </script>
      ';
    }

    return $value;
  }

  public function content(): string 
  {
    global $participants, $events, $filtered_activities, $colors;
    $mod_payments = new Payments();
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

    // Buscamos los pagos pendientes del usuario
    $where    = ' where user_id = ' . $_SESSION['app']['user']['user_id'] . ' and payment_date > now()';
    $payments = $mod_payments->GetRows( $where );

    foreach( $payments as $payment )
    {
      // Calculamos la diferencia de días entre la fecha del pago y la actual
      $payment_date     = new DateTime( $payment['payment_date'] );
      $diff_date        = ( new DateTime() )->diff( $payment_date );
      $difference_days  = intval( $diff_date->format( '%r%a' ) ); // Incluye signo positivo o negativo

      // Determinamos el color del badge
      if( $difference_days > 30 )
        $badge_color = 'green'; // Más de 30 días antes de la fecha
      elseif ( $difference_days >= 0 )
        $badge_color = 'orange'; // Entre 0 y 30 días antes
      else
        $badge_color = 'red'; // Más de 30 días después

      $widget_reminders[] = [
          'title' => pl_label( 'pending_payment' ) . ' - ' . $payment['payment_date']
        , 'color' => 'bg-' . $badge_color . '-400'
      ];
    }

    $today = date( 'Y-m-d' );
    $calendar_widget = new CalendarWidget( $widget_activities, $widget_reminders, $today );
    $value = $calendar_widget->render();
    
    return $value;
  }
  
  /**
   * Guarda los días seleccionados en el calendario para cada participante.
   * 
   * @param array $fields Datos recibidos desde la llamada AJAX.
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_save_selected_days( array $fields ): array
  {
    $value  = [];
    $db     = new Model();

    // Inicializamos las variables de la llamada AJAX
    $result   = 0;
    $message  = '';
    $redirect = '';
    $elements = [];

    do
    {
      // ------------------------------------------------------------------------------
      // Verificación de campos
      // ------------------------------------------------------------------------------
      if( !isset( $fields['selected_days'] ) || empty( $fields['selected_days'] ) )
      {
        $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': selected_days' );
        break;
      }

      // Decodificar el JSON de los días seleccionados
      $selected_days_data = json_decode( $fields['selected_days'], true );
      if( !is_array( $selected_days_data ) )
      {
        $elements = app_generate_alert( true, pl_label( 'invalid_data' ) );
        break;
      }

      // ------------------------------------------------------------------------------
      // Validación: Se deben rellenar los calendarios de todos los participantes
      // ------------------------------------------------------------------------------
      $mod_participants = new Participants();

      // Capturamos los participantes del usuario
      $participants = $mod_participants->GetRows( $_SESSION['app']['user']['user_id'] );
      $missing_participants = [];

      foreach( $participants as $participant )
      {
        $participant_id2 = $participant['participant_id2'];

        // Verificar si el participante tiene días seleccionados
        if( !isset( $selected_days_data[$participant_id2] ) || empty( $selected_days_data[$participant_id2] ) )
          $missing_participants[] = $participant['participant_name'];
      }

      // Si hay participantes sin fechas seleccionadas, mostramos una alerta
      if( count( $missing_participants ) > 0 )
      {
        $elements = app_generate_alert( true, pl_label( 'missing_schedule' ) . ': ' . implode( ', ', $missing_participants ) );
        break;
      }

      // ------------------------------------------------------------------------------
      // Procesamiento de cada calendario
      // ------------------------------------------------------------------------------
      foreach( $selected_days_data as $participant_id2 => $dates )
      {
        // Capturamos el id del participante
        $participant = $mod_participants->GetRow( $participant_id2 );
        if( $participant[0] )
          $participant_id = $participant[0]['participant_id'];
        else
        {
          $elements = app_generate_alert( true, pl_label( 'participant_not_exists' ) . ': ' . $participant_id2 );
          break 2;
        }
        
        if( !is_array( $dates ) || empty( $dates ) )
          continue;

        // Convertir el array de fechas en grupos consecutivos
        $schedule_days = app_organize_dates( $dates );
        foreach( $schedule_days as $group )
        {
          // Capturar la primera y última fecha del grupo
          $start_date = array_shift( $group );
          $end_date   = count( $group ) > 0 ? array_pop( $group ) : $start_date;

          // Insertar las fechas en la base de datos
          $sql = '
            insert into ' . DB_PROJECT . '.schedule_participants (
                schedule_id2
              , participant_id
              , start_day
              , end_day
            ) values ( ?, ?, ?, ? )
          ';
          
          $params = [
              pl_random()   // Generar un ID aleatorio único
            , $participant_id
            , $start_date
            , $end_date
          ];
          
          $db->pl_query_prepared( $sql, $params );
        }
      }

      // Guardamos en la DB que el usuario no debe volver a rellenar este modal
      $sql = '
        update ' . DB_PROJECT . '.users set
          has_schedule = 1
        where
          user_id = ?
      ';
      
      $params = [$_SESSION['app']['user']['user_id']];
      $db->pl_query_prepared( $sql, $params );

      // ------------------------------------------------------------------------------
      // Respuesta
      // ------------------------------------------------------------------------------
      $result = 1;
      $elements = app_generate_alert( false, pl_label( 'changes-applied' ) );
      $elements = array_merge( $elements, [
        ['selector' => '#modal', 'method_name'  => 'remove']
      ] );

    } while( false );

    $value = [
        'result'    => $result
      , 'message'   => $message
      , 'redirect'  => $redirect
      , 'elements'  => $elements
    ];

    $db->close();
    return $value;
  }
}

?>