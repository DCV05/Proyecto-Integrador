$( document ).ready( function() {

  // Evento para redirección según la fila
  $( '.table-row-link' ).on( 'click', function( e ) {
    if( !$( e.target ).closest( '.edit-icon, input, button' ).length )
      window.location.href = $( this ).data( 'href' );
  } );

  // Capturamos el activity_id2 desde la tabla
  let aid2 = $( '#attendance_table' ).data( 'activity' );

  // Evento para marcar Check-in
  $( document ).on( 'click', '.btn-checkin', function() {
    let pid2      = $( this ).data( 'pid2' );
    let datetime  = new Date().toISOString().slice( 0, 16 );

    // Llamamos a la función pasando un objeto con los datos
    update_attendance( { pid2: pid2, aid2: aid2, type: 'checkin', datetime: datetime } );
  } );

  // Evento para marcar Check-out
  $( document ).on( 'click', '.btn-checkout', function() {
    let pid2      = $( this ).data( 'pid2' );
    let datetime  = new Date().toISOString().slice( 0, 16 );

    update_attendance( { pid2: pid2, aid2: aid2, type: 'checkout', datetime: datetime } );
  } );

  // Evento para actualizar el Check-in o Check-out al cambiar el input
  $( document ).on( 'change', '.checkin-input, .checkout-input', function() {
    let pid2      = $( this ).data( 'pid2' );
    let datetime  = $( this ).val();
    let type      = $( this ).hasClass( 'checkin-input' ) ? 'checkin' : 'checkout';

    update_attendance( { pid2: pid2, aid2: aid2, type: type, datetime: datetime } );
  } );

} );

function update_attendance( data ) {

  let payload = {
      pid2      : data.pid2
    , aid2      : data.aid2
    , type      : data.type
    , datetime  : data.datetime
  };

  // Ejecutamos la función AJAX
  pl_ajax_post( 'update_attendance', payload )
    .then( function( data ) {

      // Si el resultado es correcto, mostramos los popups
      if( data.result = 1 && data.elements ) {
        pl_dom( data.elements );
      }
      else
        generate_error_message( form, data.message );
    } )
    .catch( function( error ) {
      // Manejo de errores
      console.error( error );
    } );
}

window.highlight_row = function( kwargs ) {
  if( !kwargs.elem ) return; // Evita errores si el elemento no existe

  // Agrega la clase con el borde
  $( kwargs.elem ).addClass( 'bg-' + kwargs.color + '-100' );

  // Elimina el borde después de 3 segundos
  setTimeout( () => {
    $( kwargs.elem ).removeClass( 'bg-' + kwargs.color + '-100' );
  }, 3000 );
}