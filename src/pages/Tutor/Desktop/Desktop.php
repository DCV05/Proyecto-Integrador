<?php

class TutorDesktopController
{
  public function index(): void
  {
    return;
  }

  public function content(): string 
  {
    $value = '';

    // Dependiendo del tipo de usuario mostramos un formulario u otro
    $value = match( ( int ) $_SESSION['app']['user']['role'] )
    {
        0       => $this->content_tutor()
      , 1       => $this->content_monitor()
      , 2       => $this->content_admin()
      , default => ''
    };

    return $value;
  }

  public function content_tutor(): string
  {
    $value = '';  
    $db    = new pl_model();

    // Buscamos las cuentas relacionadas al usuario
    $sql = 'select * from ' . DB_PROJECT . '.user_details where user_id = ' . $_SESSION['app']['user']['user_id'];
    $db->pl_query( $sql );

    /*
      Array | account
        [detail_id] => 4
        [user_id] => 11
        [user_name] => Daniel
        [user_email] => tutor1@example.com
        [user_dni] => 34213213
        [user_phone_number] => 644753740
    */

    return $value;
  }
}

?>