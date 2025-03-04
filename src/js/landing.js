// Capturamos el height y width de la pantalla inicial
let window_height = window.innerHeight;

$( document ).ready( function() {

  // Evento para modificar el navbar según el scroll del usuario
  manage_navbar();
  $( document ).on( 'scroll', manage_navbar );

  // Recalcular el height cuando se redimensione la ventana
  $( window ).on( 'resize', function () {
    window_height = window.innerHeight;
    manage_navbar();
  } );

  // SNAP
  const container    = document.getElementById( 'snap-container' ); // Contenedor del snap
  const scrollAmount = 300; // Cantidad de desplazamiento en píxeles

  $( '#next-snap' ).click( function () {
    container.scrollBy( {left: scrollAmount, behavior: 'smooth'} );
  } );

  $( '#prev-snap' ).click( function () {
    container.scrollBy( {left: -scrollAmount, behavior: 'smooth'} );
  } );

  $( document ).on( 'click', '.apple-card', function() {
    // Capturamos el elemento overlay más cercano
    let snap_card = $( this ).find( '#overlay' );

    // Ocultamos el normal y mostramos el overlay
    $( this ).closest( '#overlay' ).toggle();
    snap_card.toggle();
  } );

} );

function manage_navbar() {
  let scroll_position = document.documentElement.scrollTop;
  let window_width    = window.innerWidth;
  let target;

  if( window_width >= 1024 )
    target = window_height;
  else if( window_width < 1024 && window_width >= 576 )
    target = window_height - 500;
  else
    target = window_height - 400;
  
  // Si el scroll sobrepasa la hero section, modificamos el navbar
  if( scroll_position >= target )
    $( '#main-navbar' ).addClass( 'bg-[#f9f9f9]' ).removeClass( 'bg-transparent' );
  else
    $( '#main-navbar' ).removeClass( 'bg-[#f9f9f9]' ).addClass( 'bg-transparent' );;
}