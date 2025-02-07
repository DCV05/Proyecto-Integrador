<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class ParticipantsController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();

    return;
  }

  // --------------------------------------------------------------------------------
  // Tabla participantes
  // --------------------------------------------------------------------------------

  /**
   * Genera la tabla de participantes asociados al usuario autenticado.
   * 
   * @return string HTML de la tabla de participantes.
   */
  public function table_participants(): string
  {
    $value            = '';
    $mod_participants = new Participants();

    // Capturamos todas las cuentas relacionadas con el usuario de la sesión
    $participants = $mod_participants->GetAll();

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'participants_table', 'class' => 'table-ui'] );

    // Columnas
    $table->addColumn( 'participant_name'             , new TableColumn( 'Name'             , ['id' => 'p_name_col']          ) );
    $table->addColumn( 'participant_birth_date'       , new TableColumn( 'Birth Date'       , ['id' => 'p_birth_date_col']    ) );
    $table->addColumn( 'participant_medical_treatment', new TableColumn( 'Medical Treatment', ['id' => 'p_medical_treatment'] ) );
    
    // Iteramos cada cuenta y la añadimos a la tabla
    foreach( $participants as $participant )
    {
      // Le damos formato a los campos
      $participant['participant_medical_treatment'] = substr( $participant['participant_medical_treatment'], 0, 50 ) . '...';

      // Definimos las celdas
      $cells = [
          'participant_name'              => new TableCell( $participant['participant_name'] )
        , 'participant_birth_date'        => new TableCell( $participant['participant_birth_date'] )
        , 'participant_medical_treatment' => new TableCell( $participant['participant_medical_treatment'], ['class' => 'max-w-[14rem]'] )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $participant['participant_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
        , 'data-href' => '/participant?pid2=' . $participant['participant_id2']
      ] ) );
    }

    // Convertimos la tabla a HTML
    $table_html = $table->html();

    // Encapsulamos
    $value = '
      <h2 class="subtitle font-semibold my-4">' . pl_label( 'participants' ) . '</h2>
      ' . $table_html . '
    ';  

    return $value;
  }
}

?>