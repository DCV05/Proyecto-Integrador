$( document ).ready( function() {

  // Abrir modal de actividad
  $( document ).on( 'click', '.open_modal_activity', function( e ) {
    e.stopPropagation();

    let card  = $( this ).closest( '.group' );
    let modal = card.find( '.card_modal_activity' );

    modal.data( 'card', card );
    modal.removeClass( 'hidden' );
    modal.appendTo( 'body' );
  } );

  // Evitar cierre al hacer clic dentro del contenido del modal
  $( document ).on( 'click', '.modal_content', function( e ) {
    e.stopPropagation();
  } );

  // Cerrar modal de actividad
  $( document ).on( 'click', '.close_modal_activity', function( e ) {
    e.stopPropagation();

    // Obtenemos el modal contenedor del botón clickado
    let modal = $( this ).closest( '.card_modal_activity' );
    close_modal( modal );
  } );

  // Cierre global de los modales de actividad al hacer clic fuera
  $( document ).on( 'click', function() {
    $( '.card_modal_activity' ).each( function() {
      // Obtenemos el modal contenedor del botón clickado
      let modal = $( this );
      close_modal( modal );
    } );
  } );

} );

function close_modal( elem ) {
  elem.addClass( 'hidden' );

  // Movemos el modal de vuelta a su tarjeta original
  let card = elem.data( 'card' );
  if( card )
    elem.appendTo( card );
}