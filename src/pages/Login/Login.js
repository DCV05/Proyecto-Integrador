$( document ).ready( function() {

  // ------------------------------------------------------------------------------
  // Formulario
  // ------------------------------------------------------------------------------

  // Evento para cuando el usuario deje de tener el focus en un input
  $( document ).on( 'focusout', 'input:not([type="button"]):not([type="radio"]):not([type="search"])', function() {
    check_inputs( $( this ) );
  } );

  // Evento del Submit
  $( '#login-form' ).submit( function( e ) {

    let has_error = false;

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Evento de checkeo en submit
    $( this ).find( 'input:not([type="button"]):not([type="radio"]):not([type="search"])' ).each( function() {
      incorrect_input = check_inputs( $( this ) );

      // Si es incorrecto, el formulario no se podrá mandar
      if( !has_error && incorrect_input )
        has_error = true;
    } );

    // Si no hay errores, enviamos el formulario
    if( has_error == false ) {
      // Capturamos los datos del formulario y los encapsulamos en un objeto
      var formdata = new FormData( this );
      var formdata_array = Object.fromEntries( formdata.entries() );
      form_submit( formdata_array );
    }
  } );
} );

// ------------------------------------------------------------------------------
// Formulario
// ------------------------------------------------------------------------------

// Función para inciar sesión en la aplicación
function form_submit( formdata ) {

  pl_ajax_post( 'login', formdata )
    .then( function( data ) {

      // Si el resultado es correcto, redirigmos al panel
      if( data.result = 1 && data.redirect > '' )
        window.location.href = data.redirect;
      else {
        let form = $( '#login-form' );
        generate_error_message( form, data.message );
      }
    } )
    .catch( function( error ) {
      // Manejo de errores
      console.error( error );
    } );
}