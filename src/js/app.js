$( document ).ready( function() {
  let open = false
  $( document ).on( 'click', '#dropdown_button', function( e ){

    // Paramos los demás eventos
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Toggle
    open = !open;

    // Ejecutamos el toggle
    if( open )
      $( '#dropdown_panel' ).removeClass( 'hidden' );
    else
      $( '#dropdown_panel' ).addClass( 'hidden' );
  } );

  $( document ).on( 'click', '.toggle-password', function() {
    let input_id = $( this ).data( 'target' ); // Captura el ID del input
    let input = $( '#' + input_id );
    let icon = $( this ).find( 'i' );

    if ( input.attr( 'type' ) === 'password' ) {
      input.attr( 'type', 'text' );
      icon.removeClass( 'fa-eye' ).addClass( 'fa-eye-slash' ); // Cambia el icono
    } else {
      input.attr( 'type', 'password' );
      icon.removeClass(' fa-eye-slash' ).addClass( 'fa-eye' ); // Vuelve al icono original
    }
  } );

  // Abrir menú hamburguesa
  $( '#hamburger_button' ).on( 'click', function() {
    $( '#hamburger_menu' ).removeClass( '-translate-x-full' ).addClass( 'translate-x-0' );
    $( '#main-navbar' ).removeClass( 'z-50' );
  });

  // Cerrar menú hamburguesa
  $( '#close_hamburger' ).on( 'click', function() {
    $( '#hamburger_menu' ).removeClass( 'translate-x-0' ).addClass( '-translate-x-full' );
    $( '#main-navbar' ).addClass( 'z-50' );
  } );

  // Cerrar menú al hacer clic fuera
  $( document ).on( 'click', function( event ) {
    if( !$( event.target ).closest( '#hamburger_menu, #hamburger_button' ).length ) {
      $( '#hamburger_menu' ).removeClass( 'translate-x-0' ).addClass( '-translate-x-full' );
      $( '#main-navbar' ).addClass( 'z-50' );
    }
  } );

} );

function check_inputs( input, show_alert = true ) {
  let has_error = false;
  let message;

  // Capturamos el valor del input
  let input_val = input.val();
  if( input_val == null || input_val == '' ) {
    message = 'Campo requerido';
    has_error = true;
  }

  // Comprobamos que el valor del input email es válido
  if( has_error == false && input.attr( 'type' ) == 'email' ) {
    if( !validate_email( input_val ) ) {
      message   = 'Email no válido';
      has_error = true;
    }
  }

  // Capturamos el ID del input, evitando errores si no existe
  let input_id = input.attr( 'id' ) || '';

  if( has_error == false && input_id.includes( 'dni' ) ) {
    if( !validate_dni( input_val ) ) {
      message   = 'DNI no válido';
      has_error = true;
    }
  }
  else if( has_error == false && input_id.includes( 'phone' ) ) {
    if( !validate_phone_number( input_val ) ) {
      message   = 'Teléfono no válido';
      has_error = true;
    }
  }

  // Mostramos la alerta si es necesario
  if( has_error == true && show_alert == true )
    generate_error_message( input.parent(), message );

  return has_error;
}

// Función para generar mensajes de error
function generate_error_message( elem, alert_message ) {

  // Mensaje de error
  let error_alert = `
    <div class="alert-container flex items-center p-4 my-2 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
      <svg aria-hidden="true" class="flex-shrink-0 w-5 h-5 text-red-700" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.93-11.412a.75.75 0 00-1.86 0l-.42 3.25a.75.75 0 00.74.842h1.34a.75.75 0 00.74-.842l-.42-3.25zm-.93 7.662a1 1 0 110-2 1 1 0 010 2z" clip-rule="evenodd" />
      </svg>
      <span class="sr-only">Error</span>
      <div class="ml-3 text-sm">
        ` + alert_message + `
      </div>
    </div>
  `;

  // Si no existe una alerta, la añadimos
  if( !elem.next( '.alert-container' ).length )
    elem.after( error_alert );
      
  // Borramos la alerta después de 5 segundos
  setTimeout( () => {
    elem.next( '.alert-container' ).remove();
  }, 5000 );
}

// Función para detectar un email correcto
function validate_email( email ) {
  const email_pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return email_pattern.test( email );
}

// Función para validar el formato del DNI
function validate_dni( dni ) {
  const regex = /^[0-9]{8}[A-Za-z]$/; // Formato DNI español: 8 dígitos + letra
  return regex.test( dni );
}

// Función para validar el formato del teléfono
function validate_phone_number( phone_number ) {
  const regex = /^[0-9]{9}$/;
  return regex.test( phone_number );
}

window.show_alert = function( kwargs ) {
  if( !kwargs.elem ) return; // Evita errores si el elemento no existe

  var $elem = $( kwargs.elem );

  // Aseguramos que el elemento tenga la clase de transición (si usas el CSS de arriba)
  $elem.addClass( 'alert' );

  // Muestra el elemento: establecemos display y opacidad inicial
  $elem.css( {
    display: 'flex',
    opacity: 1
  } );

  // Después de 3 segundos, iniciamos el fade out cambiando la opacidad a 0
  setTimeout( () => {
    $elem.css( 'opacity', 0 );
  }, 3000 );

  // Eliminamos el elemento después de 3.5 segundos (dando tiempo a que la transición se complete)
  setTimeout( () => {
    $elem.remove();
  }, 3500 );
}

window.highlight_row = function( kwargs ) {
  if( !kwargs.elem ) return; // Evita errores si el elemento no existe

  // Agrega la clase con el borde
  $( kwargs.elem ).addClass( 'bg-' + kwargs.color + '-100' );

  // Elimina el borde después de 3 segundos
  setTimeout( () => {
    $( kwargs.elem ).removeClass( 'bg-' + kwargs.color + '-100' );
  }, 3000 );
}