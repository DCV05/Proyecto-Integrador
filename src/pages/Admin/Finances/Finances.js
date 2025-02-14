$( document ).ready( function() {

  $( document ).on( 'click', '.email-icon', function( e ) {

    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Capturamos el tipo de item que queremos editar
    let uid2 = $( this ).data( 'uid2' );
    let pid2 = $( this ).data( 'pid2' );

    // Mandamos el email
    send_email( { 'uid2': uid2, 'pid2': pid2 } );
  } );
} );

// Función para enviar un email al usuario
function send_email( ids ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'send_email', ids )
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

window.show_alert = function( kwargs ) {
  if( !kwargs.elem ) return; // Evita errores si el elemento no existe

  var $elem = $( kwargs.elem );

  // Aseguramos que el elemento tenga la clase de transición (si usas el CSS de arriba)
  $elem.addClass( 'alert' );

  // Muestra el elemento: establecemos display y opacidad inicial
  $elem.css( {
    display: 'flex',
    opacity: 1
  } );

  // Después de 3 segundos, iniciamos el fade out cambiando la opacidad a 0
  setTimeout( () => {
    $elem.css( 'opacity', 0 );
  }, 3000 );

  // Removemos el elemento después de 3.5 segundos (dando tiempo a que la transición se complete)
  setTimeout( () => {
    $elem.remove();
  }, 3500 );
}