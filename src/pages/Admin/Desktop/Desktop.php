<?php

class AdminDesktopController
{
  public function index(): void
  {
    global $role;

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