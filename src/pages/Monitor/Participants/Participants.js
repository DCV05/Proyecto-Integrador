$( document ).ready( function() {

  // Evento para redirección según la fila
  $( document ).on( 'click', '.table-row-link', function( e ) {
    if( !$( e.target ).closest( '.edit-icon' ).length )
      window.location.href = $( this ).data( 'href' );
  } );

} );