<?php

class DebugController
{
  public function index(): void
  {
    print password_hash( 'password1', PASSWORD_DEFAULT );
    return;
  }
}

?>