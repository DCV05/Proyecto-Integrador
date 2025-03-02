let counter_tutors        = 1;
let counter_participants  = 1;

const sections = $( 'form section' );

// Índice actual de la sección visible
let current_section = 0;

// ------------------------------------------------------------------------------
// Calendario
// ------------------------------------------------------------------------------

// Array para almacenar los días seleccionados (formato "YYYY-MM-DD")
let selected_days = [];

$( document ).ready( function() {

  // Obtener el año actual
  const today         = new Date();
  const current_year  = today.getFullYear();
  
  const min_date = `${current_year - 9}-01-01`; // 1 de enero de hace 9 años
  const max_date = `${current_year - 6}-12-31`; // 31 de diciembre de hace 6 años
  
  // Aplicar restricciones a todos los inputs de tipo date
  $( 'input[type="date"]' ).attr( 'min', min_date ).attr( 'max', max_date );

  // ------------------------------------------------------------------------------
  // Formulario
  // ------------------------------------------------------------------------------

  $( document ).on( 'click', '.faq-toggle', function() {
    $( this ).next( '.faq-content' ).slideToggle();
  } );

  // Evento para cuando el usuario deje de tener el focus en un input
  $( document ).on( 'focusout', '#register-form input:not([type="button"], [type="file"])', function() {
    check_inputs( $( this ) );
  } );

  function sync_emails( source, target ) {
    let email = $( source ).val().trim();
    $( target ).val( email );
  }

  $( '#user_email, #tutor_email_0' ).on( 'input', function () {
    sync_emails( this, this.id === 'user_email' ? '#tutor_email_0' : '#user_email');
  } );

  // Evento del Submit
  $( '#register-form' ).submit( function( e ) {

    let has_error = false;

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Evento de checkeo en submit
    $( this ).find( '#register-form input:not([type="button"], [type="file"])' ).each( function() {
      incorrect_input = check_inputs( $( this ) );

      // Si es incorrecto, el formulario no se podrá mandar
      if( !has_error && incorrect_input )
        has_error = true;
    } );

    // Si no hay errores, enviamos el formulario
    if( has_error == false ) {
      // Capturamos los datos del formulario y los encapsulamos en un objeto
      let formdata = new FormData( this );
      formdata.append( 'schedule_days', selected_days );

      let formdata_array = Object.fromEntries( formdata.entries() );
      form_submit( formdata_array );
    }
  } );

  // Añadir un nuevo bloque de tutor
  $( '#add_tutor, #add_participant' ).click( function() {

    // Obtenemos el html del template
    let type = $( this ).attr( 'id' ).replace( 'add_', '' );
    let html = $( '#' + type + '_template' ).html();

    let counter = type == 'participant'
      ? counter_participants
      : counter_tutors;
  
    // Convertimos el HTML en un objeto JQuery para modificarlo
    let $html = $( html );

    // Modificamos el contador del ' + type + ' legal
    let heading_counter       = $html.find( '#legal_' + type + '_counter' );
    let heading_counter_html  = heading_counter.html();
    heading_counter.text( heading_counter_html + ' ' + ( counter + 1 ) );

    // Modificamos el name de los inputs
    $html.find( 'input:not([type="button"]), textarea' ).each( function() {

      // Modificamos el name y el id
      let name_counter = $( this ).attr( 'name' ) + '_' + counter;
      $( this ).attr( 'name', name_counter );
      $( this ).attr( 'id', name_counter );

      // Label
      if( $( this ).attr( 'type' ) == 'file' )
        $( this ).parent().attr( 'for', name_counter );
      else
        $( this ).siblings( 'label' ).attr( 'for', name_counter );
    } );

    // Añadimos el nuevo ' + type + '
    $( '#' + type + '_container' ).append( $html );

    // Añadimos el contador
    if( type == 'participant' )
      counter_participants += 1;
    else
      counter_tutors += 1;

    // Asegurar que los nuevos inputs tengan el evento de validación
    $html.find( 'input:not([type="button"], [type="file"])' ).on( 'input', update_form );

    // Disparar actualización manualmente para verificar si el botón debe habilitarse
    update_form();
  } );

  // Evento para actualizar el formulario
  $( document ).on( 'input', 'input:not([type="button"], [type="file"])', update_form );

  // Borrar un bloque de tutor
  $( document ).on( 'click', '.remove_tutor, .remove_participant', function() {
    if( $( this ).hasClass( 'remove_tutor' ) ) {
      counter_tutors -= 1;
      $( this ).closest( '.tutor_block' ).remove();
    }
    else if( $( this ).hasClass( 'remove_participant' ) ) {
      counter_participants -= 1;
      $( this ).closest( '.participant_block' ).remove();
    }

    // Disparar actualización manualmente para verificar si el botón debe habilitarse
    update_form();
  } );

  // ------------------------------------------------------------------------------
  // Next y previous
  // ------------------------------------------------------------------------------

  // Función para mostrar una sección según el índice
  function show_section( index ) {
    sections.addClass( 'hidden' );
    $( sections[index] ).removeClass( 'hidden' );
  }

  // Evento para avanzar de sección
  $( '.next-btn' ).on( 'click', function() {
    if( current_section < sections.length - 1 ) {
      current_section++;
      show_section( current_section );
    }
  } );

  // Evento para retroceder de sección
  $( '.prev-btn' ).on( 'click', function() {
    if( current_section > 0 ) {
      current_section--;
      show_section( current_section );
    }
  } );
} );

// ------------------------------------------------------------------------------
// Formularios
// ------------------------------------------------------------------------------

// Función para crear una cuenta
function form_submit( formdata ) {

  pl_ajax_post_files( 'signup', formdata )
    .then( function( data ) {

      // Si el resultado es correcto, redirigmos al panel
      if( data.result = 1 && data.redirect > '' )
        window.location.href = data.redirect;
      else
        pl_dom( data.elements );
    } )
    .catch( function( error ) {
      // Manejo de errores
      console.error( error );
    } );
}

function update_form() {
  // Capturamos el botón de la sección actual
  let next_button = $( sections[current_section] ).find( 'button.next-btn' );
  let has_error   = false;
  
  // Capturamos todos los inputs de la sección
  $( sections[current_section] ).find( 'input:not([type="button"], [type="file"])' ).each( function() {

    // Capturamos el valor del input
    let input_val = $( this ).val();
    if( input_val == null || input_val == '' )
      has_error = true;
  
    // Comprobamos que el valor del input email es válido
    if( $( this ).attr( 'type' ) == 'email' && has_error == false ) {
      // Si no es un email válido, mostramos una alerta
      if( !validate_email( input_val ) )
        has_error = true;
    }
    else if( $( this ).attr( 'id' ).includes( 'dni' ) ) {
      // Si no es un dni válido, mostramos una alerta
      if( !validate_dni( input_val ) )
        has_error = true;
    }
    else if( $( this ).attr( 'id' ).includes( 'phone_number' ) ) {
      // Si no es un dni válido, mostramos una alerta
      if( !validate_phone_number( input_val ) )
        has_error = true;
    }
  } );

  // Si tiene algún tipo de error, deshabilitamos el botón
  if( has_error )
    next_button.attr( 'disabled', true );
  else
    next_button.removeAttr( 'disabled' );
}