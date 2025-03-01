$( document ).ready( function() {

  $( document ).on( 'click', '#btn-add-participant', function( e ) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
  
    open_add_popup_participant();
  } );

  // Evento para cerrar el modal
  $( document ).on( 'click', '.close_modal', function() {
    $( '#modal' ).remove();
  } );

  // Evento focus
  $( document ).keydown( function( e ) {
    if( ( e.metaKey || e.ctrlKey ) && e.key.toLowerCase() === 'k' ) {
      e.preventDefault(); // Evita que se active la función por defecto del navegador
      $( '#f_search' ).focus();
    }
  } );

  $( document ).on( 'submit', '#filters', function( e ) {
    e.preventDefault();
    e.stopPropagation();
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

  // Evento del Submit
  $( document ).on( 'submit', '.add-participant-form', function( e ) {

    let has_error = false;

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();


    // Capturamos los datos del formulario y los encapsulamos en un objeto
    let formdata = new FormData( this );
    let formdata_array = Object.fromEntries( formdata.entries() );
    add_participant( formdata_array );
  } );

  // Evento para cerrar el modal
  $( document ).on( 'click', '.close_modal', function() {
    $( '#modal' ).remove();
  } );

  $( document ).on( 'click', '.delete-icon', function( e ) {

    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    let confirm_prompt = navigator.language = 'es-ES'
      ? '¿Estás seguro de querer borrar esta actividad?'
      : 'Are you sure you want to delete this activity?';

    let is_sure = confirm( confirm_prompt );
    if( !is_sure )
      return;

    // Capturamos el tipo de item que queremos editar
    let pid2 = $( this ).data( 'pid2' );

    // Dependiendo del tipo de item ejecutamos un método u otro
    delete_participant( { 'pid2': pid2 } );
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

// Función para abrir un popup
function open_add_popup_participant() {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'popup_add_participant' )
    .then( function( data ) {

      // Si el resultado es correcto, mostramos los popups
      if( data.result = 1 && data.elements )
        pl_dom( data.elements );
    } )
    .catch( function( error ) {
      // Manejo de errores
      console.error( error );
    } );
}

function add_participant( pid2 ) {

  // Añadimos un nuevo participante
  pl_ajax_post( 'add_participant', pid2 )
    .then( function( data ) {
      if( data.result === 1 )
        pl_dom( data.elements );
    } )
    .catch( function( error ) {
      console.error( error );
    } );
}

function delete_participant( pid2 ) {
  pl_ajax_post( 'delete_participant', pid2 )
    .then( function( data ) {
      if( data.elements )
        pl_dom( data.elements );
    } )
    .catch( console.error );
}