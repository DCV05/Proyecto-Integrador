<?php

use erguncaner\Table\Table;
use erguncaner\Table\TableCell;
use erguncaner\Table\TableColumn;
use erguncaner\Table\TableRow;

class AdminFinancesController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();

    return;
  }

  /**
   * Genera la tabla de pagos disponibles.
   * 
   * @return string HTML de la tabla de pagos.
   */
  public function table_finances(): string
  {
    $mod_payments = new Payments();
    $value        = '';

    // Capturamos todos los pagos relacionados con el usuario actual
    $payments = $mod_payments->GetRows();

    // Inicializamos la tabla y sus columnas
    $table = new Table( ['id' => 'payments_table', 'class' => 'table-ui'] );

    // Columnas
    $table->addColumn( 'payment_id2'     , new TableColumn( 'Payment ID'   , ['id' => 'p_id_col']          ) );
    $table->addColumn( 'user_id'         , new TableColumn( 'User ID'      , ['id' => 'p_user_col']        ) );
    $table->addColumn( 'amount'          , new TableColumn( 'Amount'       , ['id' => 'p_amount_col']      ) );
    $table->addColumn( 'status'          , new TableColumn( 'Status'       , ['id' => 'p_status_col']      ) );
    $table->addColumn( 'payment_date'    , new TableColumn( 'Payment Date' , ['id' => 'p_date_col']        ) );

    // Iteramos cada pago y lo añadimos a la tabla
    foreach( $payments as $payment )
    {
      // Formateo de la cantidad con dos decimales
      $payment['amount'] = number_format( $payment['amount'], 2 ) . ' €';

      // Definimos las celdas
      $cells = [
          'payment_id2'   => new TableCell( $payment['payment_id2'] )
        , 'user_id'       => new TableCell( $payment['user_id'] )
        , 'amount'        => new TableCell( $payment['amount'], ['class' => 'text-right'] )
        , 'status'        => new TableCell( ucfirst( $payment['status'] ), ['class' => 'text-center'] )
        , 'payment_date'  => new TableCell( date( 'F j, Y', strtotime( $payment['payment_date'] ) ) )
      ];

      // Añadimos la fila
      $table->addRow( new TableRow( $cells, [
          'id'        => 'row-' . $payment['payment_id2']
        , 'class'     => 'hover:bg-gray-100 cursor-pointer table-row-link'
      ] ) );
    }

    // Convertimos la tabla a HTML
    $value = $table->html();
    return $value;
  }
}