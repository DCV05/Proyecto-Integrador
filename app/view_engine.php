<?php

// -------------------------------------------------------------------------------------
// Compilador de máscaras
// -------------------------------------------------------------------------------------

class pl_view_engine
{
	// Variables necesarias para compilar la máscara
	protected string $template_path;
	protected Object $controller;
	protected array $vars = [];

	// Variables de caché
	protected bool $cache_enabled  = true;
	protected string $cache_path     = '';
	protected int $cache_lifetime = 300;

	/**
	 * En este caso, el constructor almacenará la plantilla, el directorio de la caché, y el tiempo de vida de la caché en segundos
	 * @param string $template_path
	 * @param string $cache_path
	 * @param int $cache_lifetime
	 *  */ 
	public function __construct(
			$template_path
		, $controller
		,	$cache_enabled  = true
		,	$cache_lifetime = 10
	)
	{
		$this->template_path    = $template_path;
		$this->controller				= $controller;

		$this->cache_path 			= str_replace( '/app/view_engine.php', '/storage/cache', __DIR__ );
		$this->cache_lifetime   = $cache_lifetime;
		$this->cache_enabled		= $cache_enabled;
	}

	// Le asignamos a la plantilla unas variables
	public function vars( $vars ): void
	{
		$this->vars = $vars;
	}

	/**
	 * Función de compilación
	 * @param string $template
	 * @return string $template
	 *  */ 
	public function compile( $template_html ): string
	{
		// Establecemos una expresión regular para capturar los tags
		$tag_pattern = "/\[\[\s*([^\|\]\s]+(?:\.[^\|\]\s]+)?)\s*(?:\|\s*([^\]]+))?\s*\]\]/";
		preg_match_all( $tag_pattern, $template_html, $matches );
		
		// Separar en arrays
		$tags 			= [];
		$functions 	= [];
		foreach( $matches[0] as $key => $match )
		{
			if( isset( $matches[2][$key] ) && $matches[2][$key] )
				$functions[] = $matches[1][$key] . " | " . $matches[2][$key];
			else
				$tags[] = $matches[1][$key];
		}

		// Intentamos ejecutar las funciones de la máscara
		foreach( $functions as $func_name )
		{
			$func_name = trim( $func_name );

			try
			{
				// Llamamos al método del controlador
				$func_parts 				= array_map( 'trim', explode( '|', $func_name ) );
				$callable_func_name = $func_parts[1];

				if( $callable_func_name == 'index' ) // Controlamos que vuelvan a llamar a la función index
				{
					print 'INDEX method cannot be executed again';
					exit;
				}

				// Si contiene un & al inicio del tag, se tratará de una función global de la aplicación
				if( $callable_func_name[0] == '&' )
				{
					$callable_func_name = 'app_' . substr( $callable_func_name, 1 );

					// Detectamos que la función contenga un parámetro
					if( isset( $func_parts[2] ) )
					{
						$callable_func_param = $func_parts[2];
						$func_result = $callable_func_name( $callable_func_param );
					}
					else
						$func_result	= $callable_func_name();

					// Reemplazamos la función por el resultado obtenido
					$template_html = str_replace( "[[ {$func_name} ]]", $func_result, $template_html );
				}
				else
				{
					// Detectamos que la función contenga un parámetro
					if( isset( $func_parts[2] ) )
					{
						$callable_func_param = $func_parts[2];
						$func_result = $this->controller->$callable_func_name( $callable_func_param );
					}
					else
						$func_result	= $this->controller->$callable_func_name();

					// Llamamos a la función y reemplazamos el HTMl en la máscara
					$template_html 	= str_replace( "[[ {$func_name} ]]", $func_result, $template_html );
				}
			}
			catch( Exception $e )
			{
				print $e->getMessage();
				continue;
			}
		}
		
		// Intentamos buscar las constantes
		foreach( $tags as $tag_name )
		{
			try
			{
				// Capturamos el nombre de la constante
				$tag_parts 	= explode( '.', $tag_name );
				$const_name = $tag_parts[0];
				$prop_name	= !empty( $tag_parts[1] ) ? $tag_parts[1] : '';
								
				// Comprobamos si es una constante definida
				if( defined( $const_name ) )
				{
					// Intentamos convertir a constante el string
					$constant_value = constant( $const_name );

					// Si la constante es un array y contiene la clave $prop_name
					if( is_array( $constant_value ) && isset( $constant_value[$prop_name] ) )
						$result = $constant_value[$prop_name];
					// Si la constante es un string
					elseif( is_string( $constant_value ) )
						$result = $constant_value;
					// Si no es válida, devolvemos un error
					else
						$result = '!' . $tag_name;
				}
				// Comprobamos si es una propiedad del controlador
				elseif( isset( $this->controller->$const_name ) )
				{
					$property = $this->controller->$const_name;
					$result = is_array( $property ) && isset( $property[$prop_name] ) 
						? $property[$prop_name] 
						: '!' . $tag_name;
				} 
				// Si no es válido
				else {
					$result = '!' . $tag_name;
				}

				// Renderizamos el HTML
				$template_html 	= str_replace( '[[ ' . $tag_name . ' ]]', $result, $template_html );
			}
			catch( Exception $e )
			{
				print $e->getMessage();
				continue;
			}
		}

		// Retornamos la plantilla formateada
		return $template_html;
	}

	/**
	 * Devuelve la ruta relativa del archivo caché que se va a crear
	 * @param string $template
	 *  */ 
	public function cache_file(): string
	{
		return "{$this->cache_path}/" . md5( $this->template_path ) . '.cache';
	}

	/**
	 * Método para renderizar la plantilla
	 * @param string $template
	 *  */ 
	public function render_template(): void
	{
		// Capturamos la ruta absoluta de la plantilla
		if( !file_exists( $this->template_path ) ) // Si la plantilla no existe, lo mostramos
			throw new Exception( "Error: Template not found" );

		// Generamos la ruta relativa del archivo de caché
		$cache_file = $this->cache_file();

		// Capturamos la última hora de modificación del nuevo archivo caché
		$filemtime = @filemtime( $cache_file );
		
		// Si permitimos la caché, existe una ruta relativa, y no ha pasado el tiempo de vida de la caché, leemos el archivo de la caché
		if( $this->cache_enabled && $filemtime && ( time() - $filemtime < $this->cache_lifetime ) )
		{
			readfile( $cache_file );
			exit;
		}
		else
		{
			// Capturamos el contenido del script
			ob_start();

			// Capturamos el contenido de la plantilla y la compilamos
			$html = file_get_contents( $this->template_path );
			$compiled_html = $this->compile( $html );
			echo $compiled_html;

			// Guardamos el fichero en el directorio de Caché
			if( $this->cache_enabled )
			{
				// Se verifica si el directorio de caché existe
				// Si no existe, lo creamos con permisos 755
				if( !is_dir( $this->cache_path ) )
					mkdir( $this->cache_path, 0755, true );

				// Enviamos el contenido del buffer ( ob_get_flush )
				// La función de LOCK_EX es evitar que, mientras se está escribiendo el archivo, no haya otro proceso que pueda escirbir dentro de él
				file_put_contents( $cache_file, ob_get_flush(), LOCK_EX );
			}
		}
	}
}

?>