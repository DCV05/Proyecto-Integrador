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
  } );

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

// --------------------------------------------------------------------------------------------------------------
// Calendario
// --------------------------------------------------------------------------------------------------------------

calendar_instances = [];

/**
 * Obtiene los días seleccionados desde el atributo data-selection del calendario.
 * 
 * @param {HTMLElement} calendar_el - Elemento HTML del calendario.
 * @returns {Array} Lista de días seleccionados.
 */
function get_selected_days( calendar_el ) {
  let data_selection = $( calendar_el ).attr( 'data-selection' );
  return data_selection ? JSON.parse( data_selection ) : [];
}

/**
 * Guarda los días seleccionados en el atributo data-selection del calendario.
 * 
 * @param {HTMLElement} calendar_el - Elemento HTML del calendario.
 * @param {Array} selected_days - Lista de días seleccionados.
 */
function save_selected_days( calendar_el, selected_days ) {
  $( calendar_el ).attr( 'data-selection', JSON.stringify( selected_days ) );
}

/**
 * Formatea una fecha en el formato "YYYY-MM-DD".
 * 
 * @param {Date} date - Objeto Date a formatear.
 * @returns {string} Fecha en formato "YYYY-MM-DD".
 */
function format_date( date ) {
  const year  = date.getFullYear();
  const month = String( date.getMonth() + 1 ).padStart( 2, '0' );
  const day   = String( date.getDate() ).padStart( 2, '0' );
  return `${ year }-${ month }-${ day }`;
}

/**
 * Actualiza el texto que muestra los días seleccionados en el calendario.
 * 
 * @param {HTMLElement} calendar_el - Elemento HTML del calendario.
 */
function update_display( calendar_el ) {
  let selected_days = get_selected_days( calendar_el );
  $( '#selected_days_display' ).text( selected_days.sort().join( ', ' ) );
}

/**
 * Agrega una fecha seleccionada al calendario si aún no está marcada.
 * 
 * @param {string} date_str - Fecha en formato "YYYY-MM-DD".
 * @param {HTMLElement} calendar_el - Elemento HTML del calendario.
 */
function add_date( date_str, calendar_el ) {
  let selected_days = get_selected_days( calendar_el );

  if( !selected_days.includes( date_str ) ) {
    selected_days.push( date_str );
    save_selected_days( calendar_el, selected_days );

    calendar_instances[calendar_el.id].addEvent( {
      title: '✔',         // Muestra un "tick" en la fecha seleccionada.
      start: date_str,    // Fecha del evento.
      allDay: true,       // Indica que el evento dura todo el día.
      classNames: ['selected-day'] // Clase CSS para destacar la fecha.
    } );
  }

  update_display( calendar_el );
}

/**
 * Renderiza un calendario FullCalendar en el elemento especificado.
 * 
 * @param {HTMLElement} calendar_el - Elemento HTML donde se renderizará el calendario.
 */
function render_calendar( calendar_el ) {
  
  let selected_days = get_selected_days( calendar_el );

  // Inicializa FullCalendar en la sección de horario
  const calendar = new FullCalendar.Calendar( calendar_el, {
      initialView  : 'dayGridMonth' // Vista predeterminada en modo "mes"
    , timeZone     : 'local' // Usa la zona horaria local
    , selectable   : true // Permite la selección de fechas
    , selectMirror : true // Refleja la selección antes de confirmarla
    , locale       : language // Usa el idioma definido en la variable global
    , validRange   : { start: new Date().toISOString().split( 'T' )[0], end: '2026-12-31' } // Rango de fechas permitido
    
    // Maneja la selección de un rango de fechas en el calendario
    , select: info => {
        if( info.end - info.start === 86400000 ) // Si se selecciona solo un día, no hacer nada
          return;

        let current = new Date( info.start );
        while( current < info.end ) { // Iterar sobre cada día en el rango seleccionado

          let date_str = format_date( current );
          if( !selected_days.includes( date_str ) ) {
            selected_days.push( date_str );
            calendar_instances[calendar_el.id].addEvent( {
              title: '✔', // Marca el día seleccionado con un tick
              start: date_str,
              allDay: true,
              classNames: ['selected-day']
            } );
          }

          current.setDate( current.getDate() + 1 ); // Avanzar al siguiente día
        }
        
        save_selected_days( calendar_el, selected_days );
        update_display( calendar_el );
      }

    // Maneja el clic en un día del calendario
    , dateClick: info => {
        let date_str = info.dateStr;
        let selected_days = get_selected_days( calendar_el );

        if( selected_days.includes( date_str ) ) { // Si la fecha ya está seleccionada, eliminarla
          selected_days = selected_days.filter( day => day !== date_str );

          let events = calendar_instances[calendar_el.id].getEvents();
          events.forEach( event => {
            if( event.startStr === date_str )
              event.remove();
          } );

        } else // Si no está seleccionada, agregarla
          add_date( date_str, calendar_el );

        save_selected_days( calendar_el, selected_days );
        update_display( calendar_el );
      }

    , eventColor: 'rgba(0,123,255,0.5)' // Color de fondo de los eventos seleccionados
    , events: [] // Inicialmente sin eventos
  } );

  // Guardamos la instancia correctamente en el objeto global
  calendar_instances[calendar_el.id] = calendar;

  calendar.render(); // Renderiza el calendario en el DOM

  // Forzar el ajuste de tamaño de todos los calendarios visibles
  force_resize_all_calendars();

  // Reajustar tamaños cuando la ventana cambie de tamaño
  $( window ).resize( () => {
    force_resize_all_calendars();
  } );

  // Configurar los eventos de cambio de calendario en el sidebar
  setup_sidebar_events();
}

/**
 * Fuerza el ajuste de tamaño en todos los calendarios visibles después de un breve retraso.
 */
function force_resize_all_calendars() {
  setTimeout( () => {
    $( '.calendar-container:visible' ).each( function() {
      if( calendar_instances[this.id] )
        calendar_instances[this.id].updateSize(); // Ajusta el tamaño del calendario
    } );
  }, 300 ); // Se espera 300ms para garantizar que el DOM esté completamente renderizado
}

/**
 * Fuerza el ajuste de tamaño en el primer calendario de la lista, si existe.
 */
function force_resize_first_calendar() {
  let calendar_ids = window.CALENDAR_IDS;

  setTimeout( () => {
    if( calendar_ids.length > 0 ) {
      let first_calendar_id = calendar_ids[0]; 
      if( calendar_instances[first_calendar_id] )
        calendar_instances[first_calendar_id].updateSize(); // Ajusta solo el primer calendario
    }
  }, 300 ); // Se espera 300ms para asegurar que el calendario está visible
}

/**
 * Configura los eventos para cambiar entre calendarios al hacer clic en los participantes del sidebar.
 */
function setup_sidebar_events() {
  $( document ).on( 'click', '.participant-selector', function() {
    // Ocultar todos los calendarios
    $( '.calendar-container' ).addClass( '!hidden' );

    // Remover bg-gray-100 de todos los selectores
    $( '.participant-selector' ).removeClass( 'bg-gray-100' );

    // Mostrar el calendario seleccionado
    var target = $( this ).data( 'target' );
    $( '#' + target ).removeClass( '!hidden' );

    // Agregar bg-gray-100 al selector activo
    $( this ).addClass( 'bg-gray-100' );

    if( calendar_instances[target] ) {
      setTimeout( () => {
        calendar_instances[target].updateSize();
      }, 100 );
    }
  } );
}