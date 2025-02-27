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

  // Evento para los botones de layout
  $( document ).on( 'click', '#layout_buttons button', function( e ) {
    e.preventDefault();
    e.stopImmediatePropagation();

    // Capturamos el modo layout
    let layout_mode = $( this ).attr( 'id' ).replace( 'button_', '' );
    update_layout( layout_mode );
  } );

  $( document ).on( 'click', '#btn-add-activity', function( e ) {
    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    open_add_popup();
  } );

  $( document ).on( 'click', '.edit-icon', function( e ) {

    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Capturamos el tipo de item que queremos editar
    let aid2 = $( this ).data( 'aid2' );

    // Dependiendo del tipo de item ejecutamos un método u otro
    open_popup( { 'aid2': aid2 } );
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
    let aid2 = $( this ).data( 'aid2' );

    // Dependiendo del tipo de item ejecutamos un método u otro
    delete_activity( { 'aid2': aid2 } );
  } );

  // Evento para cerrar el modal
  $( document ).on( 'click', '.close_modal', function() {
    $( '#modal' ).remove();
  } );

  // ------------------------------------------------------------------------------
  // Formulario
  // ------------------------------------------------------------------------------

  // Evento para cuando el usuario deje de tener el focus en un input
  $( document ).on( 'focusout', 'input:not([type="button"]):not([type="radio"]):not([type="search"])', function() {
    check_inputs( $( this ) );
  } );

  // Evento del Submit
  $( document ).on( 'submit', '.account-form, .activity-form', function( e ) {

    let has_error = false;

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Evento de checkeo en submit
    $( this ).find( 'input:not([type="button"]):not([type="radio"]):not([type="search"])' ).each( function() {
      incorrect_input = check_inputs( $( this ) );

      // Si es incorrecto, el formulario no se podrá mandar
      if( !has_error && incorrect_input )
        has_error = true;
    } );

    // Si no hay errores, enviamos el formulario
    if( has_error == false ) {
      
      // Capturamos los datos del formulario y los encapsulamos en un objeto
      let formdata = new FormData( this );

      if( $( this ).hasClass( 'account-form' ) ) {
        // Capturamos el data-id del formulario y loa añadimos al formdata
        let aid2 = $( this ).data( 'aid2' );
        formdata.append( 'aid2', aid2 );
  
        let formdata_array = Object.fromEntries( formdata.entries() );
        form_submit( formdata_array, this, 'edit_activity' );

      } else {
        let formdata_array = Object.fromEntries( formdata.entries() );
        form_submit( formdata_array, this, 'add_activity' );
      }
    }
  } );

} );

function update_layout( layout ) {
  // Ejecutamos la función AJAX
  pl_ajax_post( 'update_layout', { 'layout': layout } )
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

// Función para borrar una actividad
function delete_activity( aid2 ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'delete_activity', aid2 )
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

// Función para abrir un popup de la actividad
function open_popup( id2 ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'popup_activity', id2 )
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