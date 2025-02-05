
$( document ).ready( function() {

  // ------------------------------------------------------------------------------
  // Calendario
  // ------------------------------------------------------------------------------

  // Inicializa FullCalendar en la sección de horario
  const calendar_el = $( '#calendar' )[0];
  const calendar = new FullCalendar.Calendar( calendar_el, {
      initialView  : 'dayGridMonth'
    , timeZone     : 'local'
    , selectable   : false  // ❌ No permite seleccionar rangos de fechas
    , selectMirror : false  // ❌ No permite reflejar la selección
    , validRange   : { start: new Date().toISOString().split( 'T' )[0], end: '2026-12-31' }
    , events       : events // Carga los eventos existentes
    , editable     : false  // ❌ No permite mover eventos
    , eventClick   : null   // ❌ No permite hacer click en eventos
    , dateClick    : null   // ❌ No permite hacer click en fechas para agregar eventos
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