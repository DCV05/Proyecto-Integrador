<?php

class DebugController
{
  public function index(): void
  {
    return;
  }

  public function ajax_signup( array $fields ): array
  {
    // Capturamos las fechas del calendario y las organizamos por grupos
    $dates          = explode( ',', $fields['schedule_days'] );
    $schedule_days  = app_organize_dates( $dates );

    // Iteramos cada grupo
    foreach( $schedule_days as $group )
    {
      // Capturamos los mínimos y los más máximos
      $min_date = array_shift( $group );
      $max_date = count( $group ) > 0 ? array_pop( $group ) : $min_date;
    }
  }
}

?>