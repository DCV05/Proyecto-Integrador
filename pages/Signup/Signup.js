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

  // Índice actual de la sección visible
  let current_section = 0;
  const sections      = $( 'form section' );

  // Función para mostrar una sección según el índice
  function show_section( index ) {
    sections.addClass( 'hidden' );
    $( sections[index] ).removeClass( 'hidden' );
  }

  // Evento para avanzar de sección
  $( '.next-btn' ).on( 'click', function() {
    if( current_section < sections.length - 1 ) {
      current_section++;
      show_section( current_section );
    }
  } );

  // Evento para retroceder de sección
  $( '.prev-btn' ).on( 'click', function() {
    if( current_section > 0 ) {
      current_section--;
      show_section( current_section );
    }
  } );

  // Mostramos la primera sección
  show_section( current_section );
} );