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
    $db     = new Model();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Inicializamos la transacción
      $db->begin_transaction();

      // --------------------------------------------------------------------------------------------------------------
      // Verificación de campos
      // --------------------------------------------------------------------------------------------------------------

      // Verificamos que el POST contiene todos los campos requeridos
      $required_fields = [
          'participant_full_name_0'
        , 'participant_birth_date_0'
        , 'tutor_relationship_0'
        , 'tutor_full_name_0'
        , 'tutor_dni_0'
        , 'tutor_email_0'
      ];

      foreach( $required_fields as $required_field )
      {
        // En el caso de que el post no contenga todos los campos requeridos, mostramos una alerta
        if( !array_key_exists( $required_field, $fields ) )
        {
          $elements = app_generate_alert( true, pl_label( 'required_field' ) . ': ' . $required_field );
          break 2;
        }
      }

      // --------------------------------------------------------------------------------------------------------------
      // Filtrado por tutores
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

      // --------------------------------------------------------------------------------------------------------------
      // Filtrado por participantes
      // --------------------------------------------------------------------------------------------------------------

      // Capturamos los campos del participant
      $filtered_fields = array_filter( $fields, function( $field ): bool {
        return str_starts_with( $field, 'participant_' );
      }, ARRAY_FILTER_USE_KEY );

      $participants = [];
      foreach( $filtered_fields as $f_id => $f_value )
      {
        // Dividimos los parámetros y extraemos el índice
        $parts              = explode( '_', $f_id );
        $participant_index  = array_pop( $parts );
        $f_id               = str_replace( '_' . $participant_index, '', $f_id );

        // Añadimos el parámetro al participant
        $participants[$participant_index][$f_id] = $f_value;
      }

      try
      {
        // Buscamos si existe un usuario con los datos recibidos
        $sql = '
          select
            *
          from ' . DB_PROJECT . '.user_details
          where
            user_email = ?
        ';
        $params = [$fields['user_email']];
        
        $db->pl_query_prepared( $sql, $params );
        if( $db->next_row() )
        {
          $elements = app_generate_alert( true, pl_label( 'user_exists' ) . ': ' . $fields['user_email'] );
          break;
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
          ) values ( ?, ?, ?, ?, ? )
        ';
        
        $params = [
            pl_random()
          , $fields['user_email']
          , $cyphered_password
          , 0
          , 1
        ];
        
        $db->pl_query_prepared( $sql, $params );

        // Capturamos el id del nuevo usuario
        $user_id = $db->get_last_id();

        // ------------------------------------------------------------------------------
        // Tutores
        // ------------------------------------------------------------------------------

        // Insertamos cada nuevo tutor
        foreach( $tutors as $tutor_id => $tutor )
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
              , is_main
            ) values ( ?, ?, ?, ?, ?, ?, ?, ? )
          ';
          
          $is_main = $tutor_id == 0 ? 1 : 0;
          $params = [
              $detail_id2
            , $user_id
            , $tutor['tutor_full_name']
            , $tutor['tutor_relationship']
            , $tutor['tutor_email']
            , $tutor['tutor_dni']
            , $tutor['tutor_phone_number']
            , $is_main
          ];
          
          $db->pl_query_prepared( $sql, $params );

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
            $elements = app_generate_alert( true, 'error-create-dir' );
            continue;
          }
          
          // Calculamos la ruta de la imagen y el nombre final
          $source     = $tutor['tutor_image_upload']['name'];
          $extension  = pathinfo( $source, PATHINFO_EXTENSION );
          $target     = $dir . '/' . pl_number_id( $detail_id ) . '_' . $detail_id2 . '.' . $extension;
          
          // Movemos el fichero temporal al directorio final
          if( !move_uploaded_file( $tutor['tutor_image_upload']['tmp_name'], $target ) )
          {
            $elements = app_generate_alert( true, 'error-upload' );
            continue;
          }
        }

        // ------------------------------------------------------------------------------
        // Participantes
        // ------------------------------------------------------------------------------

        foreach( $participants as $participant )
        {
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
            ) values ( ?, ?, ?, ?, ?, ?, ? )
          ';

          $params = [
              $participant_id2
            , $user_id
            , $participant['participant_full_name']
            , $participant['participant_birth_date']
            , $participant['participant_allergies']
            , $participant['participant_special_needs']
            , $participant['participant_medical_treatment']
          ];

          $db->pl_query_prepared( $sql, $params );

          // Capturamos el ID del nuevo tutor
          $participant_id = $db->get_last_id();

          // ------------------------------------------------------------------------------
          // Subida de foto de perfil
          // ------------------------------------------------------------------------------

          if( !empty( $participant['participant_image_upload'] ) && $participant['participant_image_upload']['size'] > 0 )
          {
            // Generamos el nombre del directorio de destino. Si no existe lo creamos
            $dir = ASSETS_PATH . '/panel/participants';
            if( !is_dir( $dir ) && !@mkdir( $dir ) )
            {
              $elements = app_generate_alert( true, 'error-create-dir' );
              continue;
            }

            // Calculamos la ruta de la imagen y el nombre final
            $source     = $participant['participant_image_upload']['name'];
            $extension  = pathinfo( $source, PATHINFO_EXTENSION );
            $target     = $dir . '/' . pl_number_id( $participant_id ) . '_' . $participant_id2 . '.' . $extension;
            
            // Movemos el fichero temporal al directorio final
            if( !move_uploaded_file( $participant['participant_image_upload']['tmp_name'], $target ) )
            {
              $elements = app_generate_alert( true, 'error-upload' );
              continue;
            }
          }
        }
      
        // Redirigimos al usuario
        $redirect = '/login';
  
        // Si llega hasta aquí, está todo OK
        $db->commit();
        $result = 1;
        break;
      }
      catch( Exception $e )
      {
        $db->rollback();
        $elements = app_generate_alert( true, 'Error' );
        break;
      }

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