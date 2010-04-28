<?php
/**
 * MVC application init
 *
 * @author      Leonardo Vidarte <lvidarte@gmail.com>
 * @version     $Id: init.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  core
 */

// -----------------------
// Cargo la clase Registry
// -----------------------
import('core.Registry');

// -------------------------------------------
// Defino el nivel de error / Inicializo timer
// -------------------------------------------
init();

// ---------------
// BEGIN FUNCTIONS
// ---------------

//{{{ init($errorLevel=null)
/**
 * init()
 *
 * @return  void
 */
function init($errorLevel=null)
{
	
	// **************
 	// Solo PHP > 5.1
	// **************
	if (version_compare(phpversion(), '5.1.0', '<') == true)
	{
		die('PHP5.1 Only');
	}

	// **********************
 	// Defino error_reporting
	// **********************
	if ( $errorLevel != null )
	{
		error_reporting($errorLevel);
	}
	else
	{
		error_reporting(Config::phpErrorReporting);
	}

	// *****
	// Timer
	// *****
	$mtime = explode(" ", microtime());
	Registry::set('__START_TIME__', ($mtime[1] + $mtime[0])); 

}
//}}}
//{{{ import($classPath)
/**
 * import()
 *
 * Funcion que importa (al estilo java) una o mas clases.
 *
 * @param   string  $class_path
 * @return  void
 */
function import($classPath)
{

	#print($classPath);

	$classPath = Config::appDirSep . str_replace(".", Config::appDirSep, $classPath);
	$classPath = Config::appBaseDir . $classPath;
	#die($classPath);

	// Identifico si se trata de una clase o un directorio
	if (substr($classPath, -1, 1) == '*')
	{

		$classPath = substr($classPath, 0, strlen($classPath)-1);
		#die($classPath);

		// Traigo la lista de archivos del directorio
		$files = scandir($classPath);
		#die(print_r($files, true));

		// Incluyo todas las clases del directorio
		foreach ($files as $file)
		{

			if (preg_match("/(".Config::phpClassName."|".Config::phpInterfaceName.")$/", $file))
			{
				require_once($classPath . $file);
				_addPath($classPath);
			}

		}

	}
	else
	{

		// Incluyo la unica clase/interface pedida
		if (file_exists($classPath . Config::phpClassName))
		{
			require_once($classPath . Config::phpClassName);
		}
		if (file_exists($classPath . Config::phpInterfaceName))
		{
			require_once($classPath . Config::phpInterfaceName);
		}

		// Debo sacar el nombre de la clase/interface al final del path,
		// antes de pasarselo a la funcion addPath().
		// Escapo la barra y la contra barra que escapa la contra barra :P
		_addPath( preg_replace('/[^\/\\\]+$/', "", $classPath) );

	}

}
//}}}
//{{{ _addPath($path)
/**
 * _addPath()
 *
 * Esta funcion guarda en la Registry los directorios
 * de las clases que se van incluyendo a lo largo de la aplicacion.
 * 
 * La idea es evitar tener que usar import() para clases que se encuentren
 * en un mismo directorio. Cuando esto sucede se produce una llamada al
 * metodo __autoload(), el que intentara (buscando en este array)
 * cargar la clase de todas formas.
 *
 * @param   string  $path
 * @return  void
 */
function _addPath($path)
{

	if (!Registry::exists(Config::phpClassPaths))
	{
		Registry::add(Config::phpClassPaths, $path);
	}
	elseif (!Registry::in_array($path, Config::phpClassPaths))
	{
		Registry::add(Config::phpClassPaths, $path);
	}

}
//}}}
//{{{ __autoload($className)
/**
 * __autoload()
 *
 * @param   string   $className
 * @return  boolean
 */
function __autoload($className)
{

	if (Registry::exists(Config::phpClassPaths))
	{

		// Esto intenta evitar el uso de import() para classes que se
		// encuentran en el mismo directorio.
		for ($i = Registry::count(Config::phpClassPaths)-1; $i >= 0; $i--)
		{

			// Construyo el path completo de la clase/interface buscada
			$path = Registry::get_by_key(Config::phpClassPaths, $i);
			$class = $path . $className . Config::phpClassName;
			$interface = $path . $className . Config::phpInterfaceName;

			// Si existe lo incluyo y salgo
			if (file_exists($class))
			{
				require_once($class);
				break;
			}
			if (file_exists($interface))
			{
				require_once($interface);
				break;
			}

		}

	}

}
//}}}

?>
