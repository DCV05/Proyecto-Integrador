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