$( document ).ready( function() {

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
} );

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