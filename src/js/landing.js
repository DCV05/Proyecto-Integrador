// Capturamos el height de la pantalla inicial
let window_height = window.innerHeight;

$( document ).ready( function() {

  // Evento para modificar el navbar según el scroll del usuario
  manage_navbar();
  $( document ).on( 'scroll', manage_navbar );

  // SNAP
  const container    = document.getElementById( 'snap-container' ); // Contenedor del snap
  const scrollAmount = 300; // Cantidad de desplazamiento en píxeles

  $( '#next-snap' ).click( function () {
    container.scrollBy( {left: scrollAmount, behavior: 'smooth'} );
  } );

  $( '#prev-snap' ).click( function () {
    container.scrollBy( {left: -scrollAmount, behavior: 'smooth'} );
  } );

  $( document ).on( 'click', '.change-snap', function() {
    // Capturamos el elemento overlay más cercano
    let snap_card = $( this ).closest( '.apple-card' ).find( '#overlay' );

    // Ocultamos el normal y mostramos el overlay
    $( this ).closest( '.not-overlay' ).hide();
    snap_card.show();
  } );

  $( document ).on( 'click', '.revert-change-snap', function() {
    // Capturamos el elemento overlay más cercano
    let snap_card = $( this ).closest( '.apple-card' ).find( '.not-overlay' );

    // Ocultamos el normal y mostramos el overlay
    $( this ).closest( '#overlay' ).hide();
    snap_card.show();
  } );

} );

function manage_navbar() {
  let scroll_position = document.documentElement.scrollTop;

  // Si el scroll sobrepasa la hero section, modificamos el navbar
  if( scroll_position >= window_height )
    $( '#main-navbar' ).addClass( 'bg-white' ).removeClass( 'bg-transparent' );
  else
    $( '#main-navbar' ).removeClass( 'bg-white' ).addClass( 'bg-transparent' );;
}