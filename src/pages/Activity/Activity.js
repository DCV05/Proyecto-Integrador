$( document ).ready( function() {

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

  // Evento para redirección según la fila
  $( document ).on( 'click', '.table-row-link', function( e ) {
    if( !$( e.target ).closest( '.edit-icon, .delete-icon' ).length )
      window.location.href = $( this ).data( 'href' );
  } );

  $( document ).on( 'click', '#btn-add-group', function( e ) {
    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    open_add_popup();
  } );

  $( document ).on( 'click', '.delete-icon', function( e ) {

    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    let confirm_prompt = navigator.language = 'es-ES'
      ? '¿Estás seguro de querer borrar esta grupo?'
      : 'Are you sure you want to delete this group?';

    let is_sure = confirm( confirm_prompt );
    if( !is_sure )
      return;

    // Capturamos el tipo de item que queremos editar
    let gid2 = $( this ).data( 'gid2' );

    // Dependiendo del tipo de item ejecutamos un método u otro
    delete_group( { 'gid2': gid2 } );
  } );

  // Evento para cerrar el modal
  $( document ).on( 'click', '.close_modal', function() {
    $( '#modal' ).remove();
  } );

  $( document ).on( 'click', '#btn-add-participant', function() {
    let pid2 = $( '#participant_select' ).val();
    let aid2  = $( '#participants_table' ).data( 'activity' );

    if( !pid2 )
    {
      alert( "Selecciona un participante." );
      return;
    }

    add_participant( pid2, aid2 );
  } );

  // Evento del Submit
  $( document ).on( 'submit', '.add-group-form', function( e ) {

    let has_error = false;

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Capturamos los datos del formulario y los encapsulamos en un objeto
    let formdata = new FormData( this );
    let formdata_array = Object.fromEntries( formdata.entries() );
    form_submit( formdata_array, this, 'add_group' );
  } );

} );

function form_submit( formdata, form, method_name ) {

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

function add_participant( pid2, aid2 ) {

  // Añadimos un nuevo participante
  pl_ajax_post( 'add_participant', { pid2: pid2, aid2: aid2 } )
    .then( function( data ) {
      if( data.result === 1 )
        pl_dom( data.elements );
    } )
    .catch( function( error ) {
      console.error( error );
    } );
}

// Función para borrar una grupo
function delete_group( gid2 ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'delete_group', gid2 )
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
function open_add_popup() {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'popup_add', 'none' )
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