<?php

class MonitorAccountController
{
  public function index(): void
  {
    // Control de seguridad
    app_security();
    app_restrict();
    
    return;
  }

  // --------------------------------------------------------------------------------
  // Detalles de la cueanta
  // --------------------------------------------------------------------------------

  /**
   * Genera el perfil de la cuenta del usuario.
   * 
   * @return string HTML con los detalles de la cuenta y la foto de perfil.
   */
  public function account_profile(): string
  {
    // Datos del usuario desde la sesión
    $user_email = $_SESSION['app']['user']['user_email'];

    $user_photo = !empty( $files )
      ? '<img src="' . str_replace( $_SESSION['polaris']['document_root'], $_SESSION['polaris']['complex_domain'], reset( $files ) ) . '" class="w-10 h-10 mr-2 rounded-full shadow-landing border-2 border-gray-300">'
      : '<div class="flex items-center justify-center w-24 h-24 bg-blue-500 text-white font-bold rounded-full">' . ucfirst( $user_email[0] ) . '</div>';

    // Contenedor principal
    $value = '
      <div class="bg-white shadow-lg rounded-lg p-6 mb-6 flex flex-col items-center text-center">
        
        <div class="relative">
          ' . $user_photo . '
          <input type="file" id="upload_profile_photo" class="hidden" accept="image/*">
          <label for="upload_profile_photo" class="absolute bottom-0 right-0 bg-blue-500 text-white p-2 rounded-full cursor-pointer hover:bg-blue-600">
            <i class="fa-solid fa-camera"></i>
          </label>
        </div>

        <h2 class="text-2xl font-semibold text-gray-900 mt-4">' . $user_email . '</h2>

        <button id="btn-change-password" class="mt-4 bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600">
          ' . pl_label( 'change_password_button' ) . '
        </button>

      </div>
    ';

    return $value;
  }
}

?>