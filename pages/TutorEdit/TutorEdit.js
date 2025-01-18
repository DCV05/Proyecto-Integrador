$( document ).ready( function() {

  // Evento para cuando el usuario deje de tener el focus en un input
  $( ':input' ).on( 'focusout', function() {

    // Capturamos el valor del input
    let input_val = $( this ).val();
    if( input_val == null || input_val == '' ) {
      // Añadimos una clase de borde rojo para mostrar el error
      $( this ).parent().addClass( 'border-2 border-red-500 rounded-md' );
      
      // Oculamos la alerta después de 5 segundos
      setTimeout( () => {
        $( this ).parent().removeClass( 'border-2 border-red-500 rounded-md' );
      }, 5000 );
    }
  } );
} );