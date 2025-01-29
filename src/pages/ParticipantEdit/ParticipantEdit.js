$( document ).ready( function() {

  // Evento para cuando el usuario deje de tener el focus en un input
  $( 'input:not([type="button"])' ).on( 'focusout', function() {
    check_inputs( $( this ) );
  } );

  // Evento del Submit
  $( '#edit-participant-form' ).submit( function( e ) {

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Evento de checkeo en submit
    $( this ).find( 'input:not([type="button"])' ).each( function() {
      check_inputs( $( this ) );
    } );

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
  } );
} );

function check_inputs( input ) {
  let has_error = false;

  // Capturamos el valor del input
  let input_val = input.val();
  if( input_val == null || input_val == '' ) {
    generate_error_message( input.parent(), 'Campo requerido' );
    has_error = true;
  }

  // Comprobamos que el valor del input email es válido
  if( input.attr( 'type' ) == 'email' && has_error == false ) {

    // Si no es un email válido, mostramos una alerta
    if( !validate_email( input_val ) )
      generate_error_message( input.parent(), 'Email inválido' );
  }
}

// Función para generar mensajes de error
function generate_error_message( elem, alert_message ) {

  // Mensaje de error
  let error_alert = `
    <div class="alert-container flex items-center p-4 my-2 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
      <svg aria-hidden="true" class="flex-shrink-0 w-5 h-5 text-red-700" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.93-11.412a.75.75 0 00-1.86 0l-.42 3.25a.75.75 0 00.74.842h1.34a.75.75 0 00.74-.842l-.42-3.25zm-.93 7.662a1 1 0 110-2 1 1 0 010 2z" clip-rule="evenodd" />
      </svg>
      <span class="sr-only">Error</span>
      <div class="ml-3 text-sm">
        ` + alert_message + `
      </div>
    </div>
  `;

  // Si no existe una alerta, la añadimos
  if( !elem.next( '.alert-container' ).length )
    elem.after( error_alert );
      
  // Borramos la alerta después de 5 segundos
  setTimeout( () => {
    elem.next( '.alert-container' ).remove();
  }, 5000 );
}

// Función para detectar un email correcto
function validate_email( email ) {
  const email_pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return email_pattern.test( email );
}