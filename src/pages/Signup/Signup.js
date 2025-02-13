let counter     = 1;
const sections  = $( 'form section' );

// Índice actual de la sección visible
let current_section = 0;

$( document ).ready( function() {

  // ------------------------------------------------------------------------------
  // Calendario
  // ------------------------------------------------------------------------------

  // Array para almacenar los días seleccionados (formato "YYYY-MM-DD")
  let selected_days = [];

  // Función que formatea una fecha a "YYYY-MM-DD" (local)
  const format_date = date => {
    const year  = date.getFullYear();
    const month = String( date.getMonth() + 1 ).padStart( 2, '0' );
    const day   = String( date.getDate() ).padStart( 2, '0' );
    return `${ year }-${ month }-${ day }`;
  };

  // Actualiza el display de días seleccionados
  const update_display = () => {
    $( '#selected_days_display' ).text( selected_days.sort().join( ', ')  );
  };

  // Función para agregar una fecha: añade el tick (evento) en el calendario
  const add_date = date_str => {
    if( !selected_days.includes( date_str ) ) {
      selected_days.push( date_str );
      calendar.addEvent( {
        title: '✔',       // El tick a mostrar
        start: date_str,
        allDay: true,
        classNames: ['selected-day']
      } );
    }
  };

  // Inicializa FullCalendar en la sección de horario
  const calendar_el = $( '#calendar' )[0];
  const calendar = new FullCalendar.Calendar( calendar_el, {
      initialView  : 'dayGridMonth'
    , timeZone     : 'local'
    , selectable   : true
    , selectMirror : true
    , validRange   : { start: new Date().toISOString().split( 'T' )[0], end: '2026-12-31'
    , locale       : language
  },

    // Al arrastrar para seleccionar un rango
    select: info => {
      // Si la selección es de un solo día (exactamente 24 horas), no hacer nada,
      // pues el dateClick se encargará del toggle.
      if( info.end - info.start === 86400000 )
        return;
      
      let current = new Date( info.start );
      while( current < info.end ) { // info.end es exclusiva
        let date_str = format_date( current );
        if( !selected_days.includes( date_str ) ) {
          selected_days.push( date_str );
          calendar.addEvent( {
            title: '✔',
            start: date_str,
            allDay: true,
            classNames: ['selected-day']
          } );
        }

        current.setDate( current.getDate() + 1 );
      }
      update_display();
    },

    // Al hacer click en un día, se alterna (toggle) su selección
    dateClick: info => {
      let date_str = info.dateStr; // "YYYY-MM-DD"
      if( selected_days.includes( date_str ) ) {

        // Quitar la fecha
        selected_days = selected_days.filter( day => day !== date_str );
        let events = calendar.getEvents();
        events.forEach( event => {
          if( event.startStr === date_str )
            event.remove();
        } );

      } else // Agregar la fecha
        add_date( date_str );

      update_display();
    },
    eventColor: 'rgba(0,123,255,0.5)',
    events: []
  } );
  calendar.render();

  // ------------------------------------------------------------------------------
  // Formulario
  // ------------------------------------------------------------------------------

  // Evento para cuando el usuario deje de tener el focus en un input
  $( document ).on( 'focusout', '#register-form input:not([type="button"], [type="file"]), textarea', function() {
    check_inputs( $( this ) );
  } );

  $( '#user_email' ).on( 'input', function () {
    // Capturamos el valor del input
    let user_email = $( this ).val();

    // Insertamos este email en el primer tutor
    $( '#tutor_email_0' ).val( user_email );
  } );

  // Evento del Submit
  $( '#register-form' ).submit( function( e ) {

    let has_error = false;

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Evento de checkeo en submit
    $( this ).find( 'input:not([type="button"], [type="file"]), textarea' ).each( function() {
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
  $( '#add_tutor' ).click( function() {

    // Obtenemos el html del template
    let html = $( '#tutor_template' ).html();
  
    // Convertimos el HTML en un objeto JQuery para modificarlo
    let $html = $( html );

    // Modificamos el contador del tutor legal
    let heading_counter = $html.find( '#legal_tutor_counter' );
    let heading_counter_html = heading_counter.html();
    heading_counter.text( heading_counter_html + ' ' + ( counter + 1 ) );

    // Modificamos el name de los inputs
    $html.find( 'input:not([type="button"], [type="file"]), textarea' ).each( function() {

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

    // Añadimos el nuevo tutor
    $( '#tutor_container' ).append( $html );
    counter += 1;

    // Asegurar que los nuevos inputs tengan el evento de validación
    $html.find( 'input:not([type="button"], [type="file"]), textarea' ).on( 'input', update_form );

    // Disparar actualización manualmente para verificar si el botón debe habilitarse
    update_form();
  } );

  // Evento para actualizar el formulario
  $( document ).on( 'input', 'input:not([type="button"], [type="file"]), textarea', update_form );

  // Borrar un bloque de tutor
  $( document ).on( 'click', '.remove_tutor', function() {
    $( this ).closest( '.tutor_block' ).remove();
    counter -= 1;
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
      else {
        let form = $( '#register-form' );
        generate_error_message( form, data.message );
      }
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
  $( sections[current_section] ).find( 'input:not([type="button"], [type="file"]), textarea' ).each( function() {

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
  } );

  // Si tiene algún tipo de error, deshabilitamos el botón
  if( has_error )
    next_button.attr( 'disabled', true );
  else
    next_button.removeAttr( 'disabled' );
}