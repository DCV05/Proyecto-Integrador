$( document ).ready( function( ) {

  // Cuando se hace clic en una tarjeta (con la clase ".card")
  // se mueve su modal interno (".card_modal") al <body> para posicionarlo
  // de manera absoluta y se muestra.
  $( '.card' ).on( 'click', function( e ) {
    e.stopPropagation();
  
    let card  = $( this );
    let modal = card.find( '.card_modal' );
    
    // Guardamos la tarjeta original en los datos del modal,
    // para poder devolverlo a su lugar cuando se cierre.
    modal.data( 'card', card );
    
    modal.removeClass( 'hidden' );
    modal.appendTo( 'body' );
  } );

  // Si se hace clic dentro del contenido del modal (".modal_content"),
  // detenemos la propagación para evitar que el modal se cierre.
  $( document ).on( 'click', '.modal_content', function( e ) {
    e.stopPropagation();
  } );

  // Cuando se hace clic en el botón de cerrar (".close_modal")
  // se oculta el modal y se mueve de vuelta a la tarjeta original.
  $( document ).on( 'click', '.close_modal', function( e ) {
    e.stopPropagation();
    
    // Obtenemos el modal contenedor del botón clicado
    let modal = $( this ).closest( '.card_modal' );
    modal.addClass( 'hidden' );
    
    // Movemos el modal de vuelta a su tarjeta original
    let card = modal.data( 'card' );
    if( card )
      modal.appendTo( card );
  } );

  // Si se hace clic en cualquier parte fuera de los modales,
  // se ocultan todos los modales y se devuelven a su tarjeta original.
  $( document ).on( 'click', function() {
    $( '.card_modal' ).each( function() {
      let modal = $( this );
      
      // Ocultamos el modal
      modal.addClass( 'hidden' );
      
      // Movemos el modal de vuelta a su tarjeta original
      let card = modal.data( 'card' );
      if( card )
        modal.appendTo( card );
    } );
  } );
  
  // ------------------------------------------------------------------------------
  // Formulario
  // ------------------------------------------------------------------------------

  // Evento para cuando el usuario deje de tener el focus en un input
  $( document ).on( 'focusout', 'input:not([type="button"]):not([type="radio"])', function() {
    check_inputs( $( this ) );
  } );

  // Evento del Submit
  $( '.account-form, .participant-form' ).submit( function( e ) {

    let has_error = false;

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Evento de checkeo en submit
    $( this ).find( 'input:not([type="button"]):not([type="radio"])' ).each( function() {
      incorrect_input = check_inputs( $( this ) );

      // Si es incorrecto, el formulario no se podrá mandar
      if( !has_error && incorrect_input )
        has_error = true;
    } );

    // Si no hay errores, enviamos el formulario
    if( has_error == false ) {
      // Capturamos los datos del formulario y los encapsulamos en un objeto
      let formdata = new FormData( this );

      // Capturamos el data-id del formulario y loa añadimos al formdata
      let id2 = $( this ).data( 'id2' );
      formdata.append( 'id2', id2 );

      let formdata_array = Object.fromEntries( formdata.entries() );
      form_submit( formdata_array, this );
    }
  } );

} );

// Función para inciar sesión en la aplicación
function form_submit( formdata, form ) {

  let method_name;

  // Dependiendo del tipo de formulario que sea, ejecutamos un método u otro
  switch( $( form ).data( 'type' ) ) {
    case 'user':
      method_name = 'edit_tutor';
      break;

    case 'participant':
      method_name = 'edit_participant';
      break;
  
    default:
      break;
  }

  // Ejecutamos la función AJAX
  pl_ajax_post( method_name, formdata )
    .then( function( data ) {

      // Si el resultado es correcto, redirigmos al panel
      if( data.result = 1 )
        $( form ).parent().parent().addClass( 'hidden' );
      else
        generate_error_message( form, data.message );
    } )
    .catch( function( error ) {
      // Manejo de errores
      console.error( error );
    } );
}