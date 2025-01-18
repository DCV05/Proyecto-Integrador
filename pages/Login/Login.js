$( document ).ready( function() {

  // Evento para cuando el usuario deje de tener el focus en un input
  $( ':input' ).on( 'focusout', function() {

    let has_error = false;

    // Capturamos el valor del input
    let input_val = $( this ).val();
    if( input_val == null || input_val == '' ) {
      // Sleeccionamos la alerta a mostrar
      let alert_container = $( this ).parent().siblings( '#alert-container' );
      alert_container.show();
      
      // Oculamos la alerta después de 5 segundos
      setTimeout( () => {
        alert_container.hide();
      }, 5000 );

      has_error = true;
    }

    // Comprobamos que el valor del input email es válido
    if( $( this ).attr( 'id' ) == 'email' && has_error == false ) {
      let is_email = validate_email( input_val );
      if( !is_email ) {
        // Seleccionamos la alerta a mostrar
        let alert_container = $( this ).parent().siblings( '#alert-container-email' );
        alert_container.show();
        
        // Oculamos la alerta después de 5 segundos
        setTimeout( () => {
          alert_container.hide();
        }, 5000 );
      }
    }
  } );

  // Evento del Submit
  $( '#login-form' ).submit( function( e ) {

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();

    // Comprobamos el valor de todos los inputs del formulario
    $( this ).find( 'input[type="text"], input[type="password"]' ).each( function () {

      // Capturamos el valor del input actual
      let input_val = $( this ).val();

      // Comprobamos que el valor del input email es válido
      if( $( this ).attr( 'id' ) == 'email' ) {
        let is_email = validate_email( input_val );
        if( !is_email ) {
          // Seleccionamos la alerta a mostrar
          let alert_container = $( this ).parent().siblings( '#alert-container-email' );
          alert_container.show();
          
          // Oculamos la alerta después de 5 segundos
          setTimeout( () => {
            alert_container.hide();
          }, 5000 );
        }
      }

      // Verificamos si está vacío
      if( input_val == null || input_val == '' ) {
        let alert_container = $( this ).parent().siblings( '#alert-container' );
        alert_container.show();
        
        // Oculamos la alerta después de 5 segundos
        setTimeout( () => {
          alert_container.hide();
        }, 5000 );
      }
    } );

    /*
    // Si no hay errores, enviamos el formulario
    if( has_error == false ) {
      // Capturamos los datos del formulario y los encapsulamos en un objeto
      var formdata = new FormData( this );
      var formdata_array = Object.fromEntries( formdata.entries() );
      form_submit( formdata_array );
    }
    */
  } );
} );

// Función para inciar sesión en la aplicación
function form_submit( formdata ) {

  pl_ajax_post( 'login', formdata )
    .then( function( data ) {

      // Si el resultado es correcto, redirigmos al panel
      if( data.result = 1 && data.redirect > '' )
        window.location.href = data.redirect;
      else {
        let alert_container = $( '#alert-container-general' );
        let alert_message_container = $( '#alert-message' );

        // Mostramos el error
        alert_container.show();
        alert_message_container.text( data.message );
        
        // Oculamos la alerta después de 5 segundos
        setTimeout( () => {
          alert_container.hide();
        }, 5000 );
      }
    } )
    .catch( function( error ) {
      // Manejo de errores
      console.error( error );
    } );
}

function validate_email( email ) {
  const email_pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return email_pattern.test( email );
}  