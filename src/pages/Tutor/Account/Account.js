$( document ).ready( function() {

  // Obtener el año actual
  const today         = new Date();
  const current_year  = today.getFullYear();
  
  const min_date = `${current_year - 9}-01-01`; // 1 de enero de hace 9 años
  const max_date = `${current_year - 6}-12-31`; // 31 de diciembre de hace 6 años
  
  $( document ).on( 'focus', 'input[type="date"]', function() {
    if( !$( this ).data( 'minmaxSet' ) ) {
      $( this ).attr( 'min', min_date ).attr( 'max', max_date );
      $( this ).data( 'minmaxSet', true );
    }
  } );

  $( document ).on( 'click', '#btn-add-tutor, #btn-add-participant', function( e ) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
  
    ( this.id === 'btn-add-tutor' ? open_add_popup_user : open_add_popup_participant )();
  } );

  $( document ).on( 'click', '.table-row', function( e ) {
    if( !e.target ) return; // Si no existe e.target, salir de la función

    // Verificar si el elemento clicado tiene la clase 'icon'
    if( $( e.target ).hasClass( 'icon' ) ) {
      e.preventDefault();

      // Obtener el enlace padre más cercano
      var link = $( e.target ).closest( 'a' ).attr( 'href' );
      if( link ) window.location.href = link; // Redirigir manualmente
    }
  } );

  $( document ).on( 'click', '.delete-icon', function( e ) {

    // Evitamos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    let confirm_prompt = navigator.language = 'es-ES'
      ? '¿Estás seguro de querer borrarlo?'
      : 'Are you sure you want to delete this?';

    let is_sure = confirm( confirm_prompt );
    if( !is_sure )
      return;

    // Capturamos el tipo de item que queremos editar
    let id2   = $( this ).data( 'id2' );
    let type  = $( this ).data( 'type' );

    // Dependiendo del tipo de item ejecutamos un método u otro
    ( type === 'user' ? delete_user : delete_participant )( { 'id2': id2 } );
  } );

  // Evento focus en búsqueda general
  $( document ).keydown( function( e ) {
    if( ( e.metaKey || e.ctrlKey ) && e.key.toLowerCase() === 'k' ) {
      e.preventDefault();
      $( '#filters_participants #f_search' ).focus();
    }
  } );

  // Prevención de envío de formularios sin procesar AJAX
  $( document ).on( 'submit', '#filters_participants, #filters_users', function( e ) {
    e.preventDefault();
    e.stopPropagation();
  } );

  // Manejo de eventos para cada formulario de búsqueda
  $( document ).on( 'input', '#filters_participants #f_search', function() {
    let query = $( this ).val();
    form_search_participants( query );
  } );

  $( document ).on( 'input', '#filters_users #f_search', function() {
    let query = $( this ).val();
    form_search_users( query );
  } );

  // Manejo de botones de limpieza para cada formulario
  $( document ).on( 'click', '#filters_participants #clean-filters', function() {
    $( '#filters_participants #f_search' ).val( '' );
    form_search_participants( '' );
  } );

  $( document ).on( 'click', '#filters_users #clean-filters', function() {
    $( '#filters_users #f_search' ).val( '' );
    form_search_users( '' );
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
      case 'user':
        method_name = 'popup_user';
        break;

      case 'user_info':
        method_name = 'popup_user_info';
        break;

      case 'participant':
        method_name = 'popup_participant';
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
  
  // ------------------------------------------------------------------------------
  // Formulario
  // ------------------------------------------------------------------------------

  // Evento para cuando el usuario deje de tener el focus en un input
  $( document ).on( 'focusout', 'input:not([type="button"]):not([type="radio"]):not([type="search"])', function() {
    check_inputs( $( this ) );
  } );

  // Evento del Submit
  $( document ).on( 'submit', '.tutor-form, .participant-form, .add-tutor-form, .add-participant-form', function( e ) {

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
      method_name = 'edit_user';
      break;

    case 'participant':
      method_name = 'edit_participant';
      break;

    case 'add_user':
      method_name = 'add_user';
      break;

    case 'add_participant':
      method_name = 'add_participant';
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

function delete_user( id2 ) {
  pl_ajax_post( 'delete_user', id2 )
    .then( function( data ) {
      if( data.elements )
        pl_dom( data.elements );
    } )
    .catch( console.error );
}

function delete_participant( id2 ) {
  pl_ajax_post( 'delete_participant', id2 )
    .then( function( data ) {
      if( data.elements )
        pl_dom( data.elements );
    } )
    .catch( console.error );
}

function form_search_participants( query ) {
  pl_ajax_post( 'form_search_participants', { 'query': query } )
    .then( function( data ) {
      if( data.elements )
        pl_dom( data.elements );
      else
        generate_error_message( '#filters_participants', data.message );
    } )
    .catch( console.error );
}

function form_search_users( query ) {
  pl_ajax_post( 'form_search_users', { 'query': query })
    .then( function( data ) {
      if( data.elements )
        pl_dom(data.elements);
      else
        generate_error_message( '#filters_users', data.message );
    } )
    .catch( console.error );
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

// Función para abrir un popup
function open_add_popup_user( uid2 ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'popup_add_user', uid2 )
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
function open_add_popup_participant( pid2 ) {

  // Ejecutamos la función AJAX
  pl_ajax_post( 'popup_add_participant', pid2 )
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