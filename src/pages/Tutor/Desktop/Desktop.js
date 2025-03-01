$( document ).ready( function() {
  
  $( document ).on( 'submit', '#calendar-form', function( e ) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    let selected_days_data = {};

    $( '.calendar-container' ).each( function() {
      let calendar_id = this.id.replace( 'calendar-', '' ); // Extraer ID2 del calendario
      let selected_days = $( this ).attr( 'data-selection' );

      if( selected_days )
          selected_days_data[calendar_id] = JSON.parse( selected_days );
    } );

    json_data = { selected_days: JSON.stringify( selected_days_data ) };
    save_calendar_days( json_data );
  } );

  // Evento para cerrar el modal
  $( document ).on( 'click', '.close_modal', function() {
    $( '#modal' ).remove();
  } );

} );

function save_calendar_days( json_data ) {
  // Llamada AJAX para enviar los datos
  pl_ajax_post( 'save_selected_days', json_data )
    .then( function( data ) {
      if( data.elements )
        pl_dom( data.elements );
    })
    .catch( function( error ) {
      console.error( error );
    } );
}