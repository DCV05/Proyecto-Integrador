<?php

class TutorDesktopController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    app_restrict();

    return;
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
        $calendars      .= '<div id="calendar-' . $participant_id2 . '" class="calendar-container ' . $hidden . '"></div>';
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
    $value = '';

    // Dependiendo del tipo de usuario mostramos un formulario u otro
    $value = match( ( int ) $_SESSION['app']['user']['role'] )
    {
        0       => $this->content_tutor()
      , 1       => $this->content_monitor()
      , 2       => $this->content_admin()
      , default => ''
    };

    return $value;
  }

  public function content_tutor(): string
  {
    $value = '';  
    $db    = new Model();

    // Buscamos las cuentas relacionadas al usuario
    $sql = '
      select
        * 
      from ' . DB_PROJECT . '.user_details 
      where
        user_id = ?
    ';
    $params = [$_SESSION['app']['user']['user_id']];

    $db->pl_query_prepared( $sql, $params );

    /*
      Array | account
        [detail_id] => 4
        [user_id] => 11
        [user_name] => Daniel
        [user_email] => tutor1@example.com
        [user_dni] => 34213213
        [user_phone_number] => 644753740
    */

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