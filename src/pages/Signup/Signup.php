<?php

class SignupController
{
  public function index(): void
  {
    return;
  }

  /**
   * Procesa una solicitud de registro vía AJAX.
   *
   * @param array $fields Array con los datos de registro
   *
   * @return array Array que contiene:
   *  - 'result'   (int)   : 1 si el login fue exitoso, 0 si falló.
   *  - 'message'  (string): Mensaje de error o vacío si no hubo errores.
   *  - 'redirect' (string): URL de redirección después de un login exitoso.
   */
  public function ajax_signup( array $fields ): array
  {
    $value  = [];
    $db     = new pl_model();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';

    do
    {
      // --------------------------------------------------------------------------------------------------------------
      // Verificación de campos
      // --------------------------------------------------------------------------------------------------------------

      // Verificamos que el POST contiene todos los campos requeridos
      $required_fields = [
          'participant_name'
        , 'participant_birth_date'
        , 'tutor_relationship_0'
        , 'tutor_full_name_0'
        , 'tutor_dni_0'
        , 'tutor_email_0'
        , 'schedule_days'
      ];

      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          $message = pl_label( 'required_field' ) . ': ' . $required_field;
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // Signup
      // --------------------------------------------------------------------------------------------------------------

      // Capturamos los campos del tutor
      $filtered_fields = array_filter( $fields, function( $field ): bool {
        return str_starts_with( $field, 'tutor_' );
      }, ARRAY_FILTER_USE_KEY );

      $tutors = [];
      foreach( $filtered_fields as $f_id => $f_value )
      {
        // Dividimos los parámetros y extraemos el índice
        $parts        = explode( '_', $f_id );
        $tutor_index  = array_pop( $parts );
        $f_id         = str_replace( '_' . $tutor_index, '', $f_id );

        // Añadimos el parámetro al tutor
        $tutors[$tutor_index][$f_id] = $f_value;
      }

      // ------------------------------------------------------------------------------
      // Tutores
      // ------------------------------------------------------------------------------

      // Buscamos si existe un usuario con los datos recibidos
      foreach( $tutors as $tutor )
      {
        $sql = '
          select
            *
          from ' . DB_PROJECT . '.user_details
          where
            user_email = "' . $db->esc( $tutor['tutor_email'] ) . '"
        ';
        $db->pl_query( $sql );
        if( $db->next_row() )
        {
          $message = 'El usuario con el email: ' . $tutor['tutor_email'] . ' ya existe.';
          break 2;
        }
      }

      // Generamos una contraseña cifrada para el nuevo usuario
      $cyphered_password = password_hash( $fields['user_password'], PASSWORD_DEFAULT );

      // Insertamos un nuevo usuario
      $sql = '
        insert into ' . DB_PROJECT . '.users (
            user_id2
          , user_email
          , user_password
          , role
          , enabled
        ) values (
            "' . pl_random()                        . '"
          , "' . $db->esc( $fields['user_email'] )  . '"
          , "' . $db->esc( $cyphered_password )     . '"
          , 0
          , 1
        )
      ';
      $db->query( $sql );

      // Capturamos el id del nuevo usuario
      $user_id = $db->get_last_id();

      // Insertamos cada nuevo tutor
      foreach( $tutors as $tutor )
      {
        $detail_id2 = pl_random();
        $sql = '
          insert into ' . DB_PROJECT . '.user_details (
              detail_id2
            , user_id
            , user_name
            , user_relationship
            , user_email
            , user_dni
            , user_phone_number
          ) values (
              "'  . $detail_id2                               . '"
            , '   . $user_id                                  . '
            , "'  . $db->esc( $tutor['tutor_full_name'] )     . '"
            , "'  . $db->esc( $tutor['tutor_relationship'] )  . '"
            , "'  . $db->esc( $tutor['tutor_email'] )         . '"
            , "'  . $db->esc( $tutor['tutor_dni'] )           . '"
            , "'  . $db->esc( $tutor['tutor_phone_number'] )  . '"
          )
        ';
        $db->query( $sql );

        // Capturamos el ID del nuevo tutor
        $detail_id = $db->get_last_id();

        // ------------------------------------------------------------------------------
        // Subida de foto de perfil
        // ------------------------------------------------------------------------------

        if( empty( $tutor['tutor_image_upload'] ) && $tutor['tutor_image_upload']['size'] > 0 )
          continue;

        // Generamos el nombre del directorio de destino. Si no existe lo creamos
        $dir = ASSETS_PATH . '/panel/tutors';
        if( !is_dir( $dir ) && !@mkdir( $dir ) )
        {
          $message = pl_label( 'error-create-dir' );
          continue;
        }
        
        // Calculamos la ruta de la imagen y el nombre final
        $source     = $tutor['tutor_image_upload']['name'];
        $extension  = pathinfo( $source, PATHINFO_EXTENSION );
        $target     = $dir . '/' . pl_number_id( $detail_id ) . '_' . $detail_id2 . '.' . $extension;
        
        // Movemos el fichero temporal al directorio final
        if( !move_uploaded_file( $tutor['tutor_image_upload']['tmp_name'], $target ) )
        {
          $message = pl_label( 'error-upload' );
          continue;
        }
      }

      // ------------------------------------------------------------------------------
      // Participante
      // ------------------------------------------------------------------------------

      // Insertamos los datos del nuevo participante
      $participant_id2 = pl_random();
      $sql = '
        insert into ' . DB_PROJECT . '.participants (
            participant_id2
          , user_id
          , participant_name
          , participant_birth_date
          , participant_allergies
          , participant_special_needs
          , participant_medical_treatment
        ) values (
            "'  . $participant_id2                         . '"
          , '   . $user_id                                 . '
          , "'  . $fields['participant_name']              . '"
          , "'  . $fields['participant_birth_date']        . '"
          , "'  . $fields['participant_allergies']         . '"
          , "'  . $fields['participant_special_needs']     . '"
          , "'  . $fields['participant_medical_treatment'] . '"
        )
      ';
      $db->query( $sql );

      // Capturamos el ID del nuevo tutor
      $participant_id = $db->get_last_id();

      // ------------------------------------------------------------------------------
      // Subida de foto de perfil
      // ------------------------------------------------------------------------------

      if( !empty( $fields['participant_image_upload'] ) && $fields['participant_image_upload']['size'] > 0 )
      {
        // Generamos el nombre del directorio de destino. Si no existe lo creamos
        $dir = ASSETS_PATH . '/panel/participants';
        if( !is_dir( $dir ) && !@mkdir( $dir ) )
        {
          $message = pl_label( 'error-create-dir' );
          continue;
        }

        // Calculamos la ruta de la imagen y el nombre final
        $source     = $fields['participant_image_upload']['name'];
        $extension  = pathinfo( $source, PATHINFO_EXTENSION );
        $target     = $dir . '/' . pl_number_id( $participant_id ) . '_' . $participant_id2 . '.' . $extension;
        
        // Movemos el fichero temporal al directorio final
        if( !move_uploaded_file( $fields['participant_image_upload']['tmp_name'], $target ) )
        {
          $message = pl_label( 'error-upload' );
          continue;
        }
      }

      // ------------------------------------------------------------------------------
      // Calendario
      // ------------------------------------------------------------------------------

      if( $fields['schedule_days'] )
      {
        // Capturamos las fechas del calendario y las organizamos por grupos
        $dates          = explode( ',', $fields['schedule_days'] );
        $schedule_days  = app_organize_dates( $dates );
  
        // Iteramos cada grupo
        foreach( $schedule_days as $group )
        {
          // Capturamos los mínimos y los más máximos
          $start_date = array_shift( $group );
          $end_date   = count( $group ) > 0 ? array_pop( $group ) : $start_date;

          // Insertamos las horas en la base de datos
          $sql = '
            insert into ' . DB_PROJECT . '.schedule (
                schedule_id2
              , participant_id
              , start_day
              , end_day
            ) values (
                "'  . pl_random()     . '"
              , '   . $participant_id . '
              , "'  . $start_date     . '"
              , "'  . $end_date       . '"
            )
          ';
          $db->query( $sql );
        }
      }
      
      // Redirigimos al usuario
      $redirect = '/login';

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'    => $result
      , 'message'   => $message
      , 'redirect'  => $redirect
    ];

    $db->close();
    return $value;
  }

}

?>