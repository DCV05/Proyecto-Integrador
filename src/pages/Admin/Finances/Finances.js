$( document ).ready( function() {

  // Evento focus
  $( document ).keydown( function( e ) {
    if( ( e.metaKey || e.ctrlKey ) && e.key.toLowerCase() === 'k' ) {
      e.preventDefault(); // Evita que se active la función por defecto del navegador
      $( '#f_search' ).focus();
    }
  } );

  // Evento para capturar la búsqueda en tiempo real
  $( document ).on( 'input', '#f_search', function() {
      update_filters();
  } );

  // Evento para capturar cambios en el filtro de estado
  $( document ).on( 'change', '#payment-status', function() {
    update_filters();
  } );

  // Evento para limpiar los filtros
  $( document ).on( 'click', '#clean-filters', function() {
    $( '#f_search' ).val( '' );
    $( '#payment-status' ).val( '' );
    update_filters();
  } );

  // Función para actualizar los filtros y llamar a form_search()
  function update_filters() {
    let query = $( '#f_search' ).val();
    let status = $( '#payment-status' ).find( ':selected' ).val();
    form_search( { query, status } );
  }

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

  $( document ).on( 'change', '#payment-status', function() {
    
    // Capturamos los datos de la fila
    let option  = $( this ).find( ':selected' ).val();
    let pid2    = $( this ).parent().parent().attr( 'id' );
    pid2 = pid2.replace( 'row-', '' );

    // Enviamos el evento al servidor
    change_payment_status( { 'pid2': pid2, 'option': option } );
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

function change_payment_status( ids ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'change_payment_status', ids )
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

function form_search( array_search ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'form_search', array_search )
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