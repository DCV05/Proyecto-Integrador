$( document ).ready( function () {
  $( '#mobile-menu-toggle' ).on( 'click', function () {
    $( '#mobile-menu' ).toggleClass( 'hidden flex' );
  } );

  calculate_snap_width();
  $( window ).resize( calculate_snap_width );

} );

function calculate_snap_width() {

  let window_width = window.innerWidth;

  // Capturamos el offset del padre
  let details_container_offset = window_width >= 1280
    ? $( '#details-container' ).offset().left
    : $( '#details-container' ).offset().left + 15;

  // Se aplica el offset al snap-container
  let first_child = $( '#snap-container' ).find( '>:first-child' );
  first_child.css( 'margin-left', details_container_offset );
}