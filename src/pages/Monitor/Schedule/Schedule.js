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
    , locale       : language
  } );
  
  calendar.render();
  
} );  