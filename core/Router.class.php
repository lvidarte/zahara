<?php
import('core.Error');
import('core.FilePath');
import('core.ControllerBase');

/**
 * Class Router
 *
 * Esta clase es la responsable de delegar la tarea al controlador
 * correspondiente, en funcion de la URL recibida.
 * Tambien le indica al controlador la accion (el metodo) a ejecutar.
 *
 * @todo        implementar el resto de los metodos get
 *
 * @author      Leonardo Vidarte <lvidarte@gmail.com>
 * @version     $Id: Router.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  core
 * @abstract
 */
abstract class Router
{

	//{{{ Members
	//<[members]
	/**
	 * URL recibida
	 *
	 * @access  private
	 * @var     array
	 */
	private static $url = null;

	/**
	 * Lista de argumentos recibidos
	 *
	 * @access  private
	 * @var     array
	 */
	private static $args = array();

	/**
	 * Nombre de la clase controlador
	 *
	 * @access  private
	 * @var     string
	 */
	private static $class_controller = null;

	/**
	 * La ruta completa al archivo de la clase
	 *
	 * @access  private
	 * @var     string
	 */
	private static $full_path_class_controller = null;

	/**
	 * Referencia al objeto controlador
	 *
	 * @access  private
	 * @var     mixed
	 */
	private static $obj_controller = null;

	/**
	 * Nombre del metodo a ejecutar
	 *
	 * @access  private
	 * @var     string
	 */
	private static $obj_controller_method = null;
	//>
	//}}}

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ delegate()
	//<[delegate()]
	/**
	 * Instancia el controlador y llama al metodo correspondiente.
	 *
	 * @access public
	 * @return void
	 * @todo   implementar un metodo 404
	 */
	public static function delegate()
	{

		// ----------------------------------
		// URL: almacenado de la URL original
		// ----------------------------------
		self::$url = isset($_GET['route']) ? "/{$_GET['route']}" : "/";
		#die(self::$url);

		// ------------------------------------
		// SESSION: existe una session previa?
		// ------------------------------------
		if ( isset($_COOKIE[ini_get('session.name')]) )
		{

			// La recupero y guardo en el registro
			$session = new Session;
			Registry::set('__SESSION__', $session);

			// Existe un usuario guardado en la session?
			if ( $session->exists('__USER__') )
			{
				// Lo dejo a mano
				Registry::set('__USER__', $session->get('__USER__'));
			}
			
		}

		// --------------------------------------
		// Control de directorio de controladores
		// --------------------------------------
		if ( ! is_dir(Config::appBaseDir . Config::appControllerBaseDir) )
		{
			throw new Exception("Invalid ControllerBaseDir");
		}

		// -------------------------
		// Parseo de $_GET['route']
		// y determinar lo que hacer
		// -------------------------
		self::set_controller();

		// -------------------------------
		// Error 404 si la clase no existe
		// -------------------------------
		if (!is_readable(self::$full_path_class_controller))
		{

			$_message = '_404_class_not_found';

			$_description = array(
				'_404_class_not_found_description',
				self::$full_path_class_controller
			);

			$error = new Error($_message, $_description);
			$error->death();
		}

		// ---------------------
		// Inclusion de la clase
		// ---------------------
		$class = str_replace(Config::appBaseDir, "", self::$full_path_class_controller);
		$class = str_replace(Config::appDirSep, ".", $class);
		$class = str_replace(Config::phpClassName, '', $class);
		import($class);

		// -------------------------
		// Instanciacion de la clase
		// -------------------------
		#die(self::$class_controller);
		self::$obj_controller = new self::$class_controller(self::$full_path_class_controller);
		#die(self::$full_path_class_controller);

		// ---------------------------
		// Eleccion de metodo de clase
		// ---------------------------
		//
		#die(print_r(self::$args, true));
		//
		// Siguiente argumento de la URL
		if (count(self::$args) && is_callable(array(self::$obj_controller, self::$args[0])))
		{
			self::$obj_controller_method = array_shift(self::$args);
		}
		// Metodo por default (definido en Config)
		elseif (is_callable(array(self::$obj_controller, Config::phpControllerMethodDefault)))
		{
			self::$obj_controller_method = Config::phpControllerMethodDefault;
		}
		// Error: sin metodo
		else
		{
			self::$obj_controller_method = array_shift(self::$args);

			$_message = '_404_method_not_found';

			$_description = array(
				'_404_method_not_found_description',
				self::$full_path_class_controller,
				self::$obj_controller_method
			);

			$error = new Error($_message, $_description);
			$error->death();

		}

		// --------------------
		// Registro controlador
		// --------------------
		Registry::set('__CONTROLLER__', self::$obj_controller);

		// -----------------
		// Registro opciones
		// -----------------
		if (Config::dbEnabled)
			Registry::set('__OPTIONS__', new Options);

		// --------------------------------------
		// Cargo los permisos para el controlador
		// --------------------------------------
		$level_id = self::$obj_controller->get_level_id();

		// --------------------------
		// LOGIN: control de permisos
		// --------------------------
		if ( $level_id > Config::U_ALL )
		{

			// Existe el usuario?
			if ( Registry::exists('__USER__') )
			{
				$user = Registry::get('__USER__');

				// Tiene los permisos?
				if ( ! $user->has($level_id) )
				{
					$error = new Error(
						array('_user_auth_error'),
						array('_user_auth_error_description', self::get_url())
					);
					$error->death();
				}

			}
			// El usuario debe loguearse
			else
				$login = new Login;

		}

		// ---------------------------
		// Ejecucion del metodo pedido
		// ---------------------------
		$_ = sprintf("<pre>%s</pre>", print_r(self::$args, true));
		#die(self::$full_path_class_controller . '::' . self::$obj_controller_method . '(self::$args)' . $_);
		self::$obj_controller->{self::$obj_controller_method}(self::$args);

	}
	//>
	//}}}
	//{{{ set_controller()
	//<[set_controller()]
	/**
	 * Analiza la URL y determina el path completo de la clase controlador.
	 * Tambien determina el metodo (o accion) a ejecutar.
	 *
	 * @access  private
	 * @return  void
	 * @todo    implementar un metodo 404
	 */
	private function set_controller()
	{

		// Eliminado de barras delante y detras de la URL
		$route = trim(self::$url, '/');
		#die("$route");

		// Separo la URL
		$parts = explode('/', $route);
		#die(print_r($parts, true));

		// Path completo al directorio de controladores
		$cmd_path = Config::appBaseDir . Config::appControllerBaseDir;
		#die($cmd_path);

		// Busco el controlador
		foreach ($parts as $part)
		{

			$fullpath = $cmd_path . Config::appDirSep . $part;
			#die($fullpath);

			// Existe el directorio $fullpath?
			if (is_dir($fullpath))
			{
				$cmd_path .= Config::appDirSep . $part;
				array_shift($parts);
				#die($fullpath);
				continue;
			}
			#die(print_r($parts, true));

			// Traigo los archivos del dir
			$files = scandir($cmd_path);
			#die(print_r($files, true));

			// Traigo la clase correspondiente
			// Ej: PrintCode a partir de la URL /printcode
			for ($i = 0; $i < count($files); $i++)
			{
				$files[$i] = preg_replace('/'.Config::phpClassName.'$/', '', $files[$i]);
				#print($files[$i] . " ");
				if ( strtolower($files[$i]) == strtolower(Config::phpControllerClassPrefix . $part) )
				{
					self::$class_controller = $files[$i];
					#die(self::$class_controller);
					array_shift($parts);
					break;
				}
				#print($files[$i] . " ");
			}
			if ($i < count($files)) break;
			#die(print_r($parts, true));
		}

		// Si no hay controlador uso el definido por Config
		if (empty(self::$class_controller))
		{
			self::$class_controller = Config::phpControllerClassDefault;
			#die(self::$class_controller);
		}

		// Seteo path completo a la clase controlador
		self::$full_path_class_controller = $cmd_path . Config::appDirSep . self::$class_controller . Config::phpClassName;
		#die(self::$full_path_class_controller);

		// Almacenado resto tokens URL
		self::$args = $parts;

	}
	//>
	//}}}
	//{{{ get_args()
	//<[get_args()]
	/**
	 * Retorna un array con los argumentos del Request
	 *
	 * @access public
	 * @return array
	 */
	public static function get_args()
	{
		return self::$args;
	}
	//>
	//}}}
	//{{{ get_arg($offset=null)
	//<[get_arg()]
	/**
	 * Retorna un unico argumento del Request
	 *
	 * @access public
	 * @param  int     $offset  optional
	 * @return string
	 */
	public static function get_arg($offset=null)
	{
		return ($offset>=0 && isset(self::$args[$offset])) ? self::$args[$offset] : '';
	}
	//>
	//}}}
	//{{{ get_url()
	//<[get_url()]
	/**
	 * Retorna $this->url
	 *
	 * @access public
	 * @return string
	 */
	public static function get_url()
	{
		return self::$url;
	}
	//>
	//}}}
	//{{{ get_url_base()
	//<[get_url_base()]
	/**
	 * Retorna $this->url eliminando los argumentos
	 * contenidos en $this->args
	 *
	 * @access  public
	 * @return  string
	 */
	public static function get_url_base()
	{

		$args = count(self::$args);

		if ( $args )
		{
			$url = self::$url;
			for ($args; $args>0; $args--)
			{
				$token = '\/'.self::$args[$args-1];
				$url = preg_replace('/'.$token.'$/','',$url);
			}
			return $url;
		}
		else
		{
			return self::$url;
		}
	}
	//>
	//}}}
	//{{{ redir()
	//<[redir()]
	/**
	 * Wrapper de la funcion header()
	 *
	 * @access  public
	 * @param   string  $location
	 * @return  void
	 */
	public static function redir($location)
	{

		header("Location: $location");
		die();

	}
	//>
	//}}}

}
?>
