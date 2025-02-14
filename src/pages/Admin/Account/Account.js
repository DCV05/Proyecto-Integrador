$( document ).ready( function() {

  // Evento para redirección según la fila
  $( document ).on( 'click', '.table-row-link', function( e ) {
    if( !$( e.target ).closest( '.edit-icon' ).length )
      window.location.href = $( this ).data( 'href' );
  } );

  $( document ).on( 'click', '.edit-icon', function( e ) {

    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Capturamos el tipo de item que queremos editar
    let type  = $( this ).data( 'type' );
    let id2   = $( this ).data( 'id2' );

    // Dependiendo del tipo de item ejecutamos un método u otro
    let method_name = type == 'user' ? 'popup_user' : 'popup_participant';
    open_popup( method_name, { 'id2': id2 } );
  } );

  // Evento para cerrar el modal
  $( document ).on( 'click', '.close_modal', function() {
    $( '#modal' ).remove();
  } );
  
  // ------------------------------------------------------------------------------
  // Formulario
  // ------------------------------------------------------------------------------

  // Evento para cuando el usuario deje de tener el focus en un input
  $( document ).on( 'focusout', 'input:not([type="button"]):not([type="radio"])', function() {
    check_inputs( $( this ) );
  } );

  // Evento del Submit
  $( document ).on( 'submit', '.account-form, .participant-form', function( e ) {

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
      if( data.result = 1 && data.elements ) {
        pl_dom( data.elements );
        $( '#modal' ).remove();
      }
      else
        generate_error_message( form, data.message );
    } )
    .catch( function( error ) {
      // Manejo de errores
      console.error( error );
    } );
}

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