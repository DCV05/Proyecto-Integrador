<?php

class MonitorDesktopController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    app_restrict();

    return;
  }

  public function content(): string 
  {
    $value = '';

    return $value;
  }
}

?>