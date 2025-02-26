$( document ).ready( function() {

  // Evento para cuando el usuario deje de tener el focus en un input
  $( document ).on( 'focusout', '#contact-form input:not([type="button"]), #contact-form textarea', function() {
    check_inputs( $( this ) );
  } );

  // Comprobamos el estado del formulario en cada input
  $( document ).on( 'input', '#contact-form input:not([type="button"]), #contact-form textarea', function() {
    update_form();
  } );

  $( document ).on( 'submit', '#contact-form', function( e ) {

    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
      
    // Capturamos los datos del formulario y los encapsulamos en un objeto
    let formdata = new FormData( this );

    // Capturamos los datos del formulario
    let formdata_array = Object.fromEntries( formdata.entries() );
    send_email( formdata_array );
  } );

} );

function update_form() {
  // Capturamos el botón de la sección actual
  let submit_button = $( '#contact-form button' );
  let has_error     = false;
  
  // Capturamos todos los inputs de la sección
  $( '#contact-form input:not([type="button"]), #contact-form textarea' ).each( function() {

    // Capturamos el valor del input
    let input_val = $( this ).val();
    if( input_val == null || input_val == '' )
      has_error = true;
  
    // Comprobamos que el valor del input email es válido
    if( $( this ).attr( 'type' ) == 'email' && has_error == false ) {
      // Si no es un email válido, mostramos una alerta
      if( !validate_email( input_val ) )
        has_error = true;
    }
  } );

  // Si tiene algún tipo de error, deshabilitamos el botón
  if( has_error )
    submit_button.attr( 'disabled', true );
  else
    submit_button.removeAttr( 'disabled' );
}

// Función para enviar un email
function send_email( formdata ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'send_email', formdata )
    .then( function( data ) {

      // Si el resultado es correcto, mostramos los popups
      if( data.result = 1 && data.elements )
        pl_dom( data.elements );
      else
        generate_error_message( form, data.message );
    } )
    .catch( function( error ) {
      // Manejo de errores
      console.error( error );
    } );
}