
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
    initialView  : 'dayGridMonth',
    timeZone     : 'local',
    selectable   : true,
    selectMirror : true,
    validRange   : { start: new Date().toISOString().split( 'T' )[0], end: '2026-12-31' },

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

  // ------------------------------------------------------------------------------------------------------
  // AJAX
  // ------------------------------------------------------------------------------------------------------

  // Evento del Submit
  $( '#register-form' ).submit( function( e ) {

    let has_error = false;

    // Evitamos el submit
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Evento de checkeo en submit
    $( this ).find( 'input:not([type="button"])' ).each( function() {
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

} );

// ------------------------------------------------------------------------------
// Formularios
// ------------------------------------------------------------------------------

// Función para crear una cuenta
function form_submit( formdata ) {

  pl_ajax_post_files( 'signup', formdata )
    .then( function( data ) {

      // Si el resultado es correcto, redirigmos al panel
      if( data.result = 1 && data.redirect > '' && false )
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