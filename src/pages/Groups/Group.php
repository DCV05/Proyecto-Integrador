<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class GroupController
{

  public string $group_id2;

  public function index(): void
  {
    // Control de seguridad
    app_security();

    // Capturamos el id2 del grupo
    $this->group_id2 = pl_get( 'gid2', null );
    if( !$this->group_id2 )
      pl_redirect( '/groups' );

    return;
  }

  // --------------------------------------------------------------------------------
  // Tabla participantes
  // --------------------------------------------------------------------------------

  /**
   * Genera la tabla de participantes.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_participants( string $where = '' ): string
  {
    global $role;

    $value                    = '';
    $mod_groups_participants  = new GroupParticipants();

    // Capturamos los grupos
    $group_participants = $mod_groups_participants->GetRow( $this->group_id2, $where );

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'participants_table', 'class' => 'p-table'] );

    // Columnas
    $table->addColumn( 'participant_name'             , new TableColumn( pl_label( 'name' )              ) );
    $table->addColumn( 'participant_birth_date'       , new TableColumn( pl_label( 'birth_date' )        ) );
    $table->addColumn( 'participant_allergies'        , new TableColumn( pl_label( 'allergies' )         ) );
    $table->addColumn( 'participant_special_needs'    , new TableColumn( pl_label( 'special_needs' )     ) );
    $table->addColumn( 'participant_medical_treatment', new TableColumn( pl_label( 'medical_treatment' ) ) );
    $table->addColumn( 'schedule_icon'                , new TableColumn( ''                              ) );

    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $group_participants as $participant )
    {
      // Le damos formato a los campos
      if( str_word_count( $participant['participant_medical_treatment'] ) > 50 )
        $medical_treatment = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';
      else
        $medical_treatment = $participant['participant_medical_treatment'];

      // Botón de calendario
      $schedule_icon = '
        <a href="/participant?pid2=' . $participant['participant_id2'] . '" class="p-button">
          ' . app_get_svg_icon( 'schedule' ) . '
        </a>
      ';

      // Definimos las celdas
      $cells = [
          'participant_name'              => new TableCell( $participant['participant_name'] )
        , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
        , 'participant_allergies'         => new TableCell( $participant['participant_allergies'] )
        , 'participant_special_needs'     => new TableCell( $participant['participant_special_needs'] )
        , 'participant_medical_treatment' => new TableCell( $medical_treatment, ['class' => 'max-w-[14rem]'] )
        , 'schedule_icon'                 => new TableCell( $schedule_icon, ['class' => 'text-center w-12 icon-container schedule-icon'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $participant['participant_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row'
        , 'data-type' => 'participant_info'
        , 'data-id2'  => $participant['participant_id2']
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
    return $value;
  }

  // --------------------------------------------------------------------------------
  // AJAX
  // --------------------------------------------------------------------------------

  public function ajax_form_search( array $fields ): array
  {
    $value  = [];

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Filtro
      $where = ' and p.participant_name like "%' . $fields['query'] . '%"';

      // Recargamos el HTML
      $html = $this->table_participants( $where );

      // Rellenamos los objetos a actualizar
      $elements = [
        ['selector' => '#participants_table', 'method_name'  => 'update' , 'value' => $html]
      ];

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'   => $result
      , 'message'  => $message
      , 'redirect' => $redirect
      , 'elements' => $elements
    ];

    return $value;
  }

  /**
   * Obtiene y muestra un popup con los detalles del usuario seleccionado.
   * 
   * @param array $fields Contiene el identificador del usuario (`id2`).
   * @return array Respuesta con resultado, mensaje, redirección y elementos a modificar en el DOM.
   */
  public function ajax_popup_participant_info( array $fields ): array
  {
    $value           = [];
    $mod_participant = new Participants();

    // Inicializamos las variables de la llamada AJAX
    $result     = 0;
    $message    = '';
    $redirect   = '';
    $elements   = [];

    do
    {
      // Buscamos los datos del participante solicitado
      $participant = $mod_participant->GetRow( $fields['id2'] );
      if( empty( $participant ) )
        break;

      $participant = $participant[0];

      /*
        Array | participant
          [participant_id] => 5
          [participant_id2] => abc123
          [user_id] => 11
          [participant_name] => Carlos
          [participant_birth_date] => 2015-06-21
          [participant_allergies] => Ninguna
          [participant_special_needs] => Ninguna
          [participant_medical_treatment] => No aplica
      */

      // Formulario
      $html = '
        <div id="modal" class="card_modal hidden absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div class="modal_content relative bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh]">

            <h3 class="text-2xl mb-4">' . pl_label( 'participant' ) . '</h3>

            <button class="close_modal absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Cerrar">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <div class="modal_content space-y-5">
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_name' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $participant['participant_name'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_birth_date' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md">' . $participant['participant_birth_date'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_allergies' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md min-h-[100px]">' . $participant['participant_allergies'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_special_needs' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md min-h-[100px]">' . $participant['participant_special_needs'] . '</div>
              </div>
              <div>
                <label class="custom-label block text-sm font-medium text-gray-700">' . pl_label( 'participant_medical_treatment' ) . '</label>
                <div class="custom-input mt-1 transform transition duration-300 bg-gray-100 p-2 rounded-md min-h-[100px]">' . $participant['participant_medical_treatment'] . '</div>
              </div>
            </div>

          </div>
        </div>
      ';

      // Rellenamos los objetos a actualizar
      $elements = [
          ['selector' => 'body'       , 'method_name' => 'append', 'value' => $html]
        , ['selector' => '.card_modal', 'method_name' => 'css'   , 'value' => 'flex', 'css' => 'display']
      ];

      // Si llega hasta aquí, está todo OK
      $result = 1;
      break;

    } while( false );
    
    $value = [
        'result'   => $result
      , 'message'  => $message
      , 'redirect' => $redirect
      , 'elements' => $elements
    ];

    return $value;
  }
}

?>