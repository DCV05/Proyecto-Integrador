$( document ).ready( function() {

  // Evento focus
  $( document ).keydown( function( e ) {
    if( ( e.metaKey || e.ctrlKey ) && e.key.toLowerCase() === 'k' ) {
      e.preventDefault(); // Evita que se active la función por defecto del navegador
      $( '#f_search' ).focus();
    }
  } );

  $( document ).on( 'input', '#f_search', function() {
    let query = $( this ).val();
    form_search( query );
  } );

  $( document ).on( 'click', '#clean-filters', function() {
    $( '#f_search' ).val( '' );
    form_search( '' );
  } );

  $( document ).on( 'click', '.table-row', function( e ) {
    if( !e.target ) return; // Si no existe e.target, salir de la función

    // Verificar si el elemento clicado tiene la clase 'p-button'
    if( $( e.target ).parent().hasClass( 'p-button' ) ) {
      e.preventDefault();

      // Obtener el enlace padre más cercano
      var link = $( e.target ).closest( 'a' ).attr( 'href' );
      if( link ) window.location.href = link; // Redirigir manualmente
    }
  } );

  $( document ).on( 'click', '.edit-icon, .table-row', function( e ) {

    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Capturamos el tipo de item que queremos editar
    let type  = $( this ).data( 'type' );
    let id2   = $( this ).data( 'id2' );

    // Dependiendo del tipo de item ejecutamos un método u otro
    let method_name;
    switch( type ) {

      case 'user_info':
        method_name = 'popup_user_info';
        break;

      case 'participant_info':
        method_name = 'popup_participant_info';
        break;
    
      default:
        break;
    }

    // Mandamos la petición
    open_popup( method_name, { 'id2': id2 } );
  } );

  // Evento para cerrar el modal
  $( document ).on( 'click', '.close_modal', function() {
    $( '#modal' ).remove();
  } );

} );

// Función para inciar sesión en la aplicación
function open_popup( method_name, id2 ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( method_name, id2 )
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

function form_search( query ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'form_search', { 'query': query } )
    .then( function( data ) {

      // Si el resultado es correcto, redirigmos al panel
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