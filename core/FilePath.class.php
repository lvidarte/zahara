<?php
/**
 * Class FilePath
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: FilePath.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 */
class FilePath
{

	//{{{ Members
	/**
	 * Almacena el string con el path
	 * completo al archivo.
	 *
	 * @var	   string
	 * @access  private
	 */
	private $_str_file_path = null;

	/**
	 * Contiene, del archivo permissions.conf,
	 * unicamente las lineas donde se definen permisos.
	 * Es decir, no tiene los comentarios, ni los alias
	 * ni tampoco las lineas en blanco.
	 *
	 * @var     Array
	 * @access  private
	 */
	private $_permissions_conf = array();
	//}}}

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct($file_path)
	//<[__construct()]
	/**
  public
	 * @param   string   $str_file_path   La ruta completa al archivo que
	 *                                    instancio el la clase.
	 * @return  void
	 */
	public function __construct($str_file_path)
	{


		if ( ! file_exists($str_file_path) )
		{

			$_description = array(
				'_404_file_path_error_description',
				$str_file_path
			);

			$error = new Error('_404_file_path_error', $_description);
			$error->death();

		}

		$this->_str_file_path = $str_file_path;


	}
	//>
	//}}}
	//{{{ get_controller_base()
	//<[get_controller_base]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_controller_base()
	{

		return Config::appBaseDir . Config::appControllerBaseDir;

	}
	//>
	//}}}
	//{{{ get_dir_path()
	//<[get_dir_path()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_dir_path()
	{

		return preg_replace(
			'/[^\/]+$/',
			'',
			$this->_str_file_path
		);

	}
	//>
	//}}}
	//{{{ get_file_name()
	//<[get_file_name()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_file_name()
	{

		return preg_replace(
			'/^' . str_replace('/','\/',$this->get_dir_path()) . '/',
			'',
			$this->_str_file_path
		);

	}
	//>
	//}}}
	//{{{ get_class_name()
	//<[get_class_name()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_class_name()
	{

		if ( preg_match('/'.Config::phpClassName.'$/', $this->get_file_name()) )
		{
			return preg_replace(
				'/'.Config::phpClassName.'$/',
				'',
				$this->get_file_name()
			);
		}
		return false;

	}
	//>
	//}}}
	//{{{ get_web_dir()
	//<[get_web_dir()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_web_dir()
	{

		#die($this->get_controller_base());
		#die($this->get_dir_path());
		return preg_replace(
			'/^' . str_replace('/','\/',$this->get_controller_base()) . '/',
			'',
			$this->get_dir_path()
		);

	}
	//>
	//}}}
	//{{{ get_url()
	//<[get_url()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_url()
	{

		return Router::get_url();

	}
	//>
	//}}}
	//{{{ get_dirs_links()
	//<[get_dirs_links()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function get_dirs_links()
	{

		$html = new HTML;

		$_dir  = preg_replace('/^\//', '', $this->get_web_dir());
		$_dir  = preg_replace('/\/$/', '', $_dir);
		$_dirs = explode('/', $_dir);

		$_base    = '/';
		$_indexes = '';
		foreach ($_dirs as $_dir)
		{
			$_base    .= "$_dir/";
			$_indexes .= $html->a('root',"$_base|$_dir") . '/';
			
		}

		return $_indexes;

	}
	//>
	//}}}
	//{{{ get_level_id()
	//<[get_level_id()]
	/**
	 * @access  public
	 * @return  int
	 */
	public function get_level_id()
	{

		// Desarmo el path
		$dirs = explode('/', $this->get_url());
		#TPL::show(Utils::dump($dirs));

		// Recorro el path de atras hacia adelante
		// en busca de un permiso
		for ($i = count($dirs); $i > 0; $i--)
		{

			// Array con los dirs + clase, metodo
			$_path = implode('/', $dirs);

			// Cuando llega al final
			if (!$_path) $_path = '/';

			// Permiso requerido para el path
			$level_id = $this->_get_level_id($_path);

			if ($level_id >= 0)
				return $level_id;
			else
				// Elimino ultimo elemento del array
				array_pop($dirs);

		}

		// No existen permisos para el path
		// (incluso no existe para /)
		return Config::U_ALL;
		
	}
	//>
	//}}}
	//{{{ _get_level_id($path)
	//<[_get_level_id()]
	/**
	 * Metodo que obtiene las restricciones
	 * para el path pasado como parametro.
	 * Para ello lee el archivo permissions.conf.
	 *
	 * @access  private
	 * @param   string   $path
	 * @return  int
	 */
	private function _get_level_id($path)
	{

		// Acumulador de permisos
		$level_id = 0;

		// Ref al array con permisos
		$_ =& $this->_permissions_conf;

		// 
		if (empty($_))
		{
			// Lectura archivo
			$_aux = file(
				Config::appBaseDir.Config::appConfigBaseDir.'/permissions.conf',
				(FILE_IGNORE_NEW_LINES + FILE_SKIP_EMPTY_LINES)
			);

			// Borrado lineas vacias y con comentarios
			for ($i = 0; $i < count($_aux); $i++)
				if (!preg_match('/^#/', ltrim($_aux[$i])))
					$_ []= trim($_aux[$i]);
		}
		#TPL::show(Utils::dump($this->_permissions_conf));

		// *******************
		// BUSQUEDA DE PERMISO
		// *******************
		for ($i = 0; $i < count($_); $i++)
		{

			// Lectura de linea
			list($url, $levels) = preg_split('/\s+/', $_[$i], 2);

			// Coincide con lo pedido?
			if ($url == $path) 
			{
				// Obtengo el listado de permisos 
				$levels = preg_split('/[\s,]+/', trim($levels));

				// Y los sumo ..
				foreach ($levels as $level)
					$level_id += eval("return Config::{$level};");

				// Retorno el permiso requerido
				return $level_id;

			}

		}

		// Salida sin resultados
		return -1;

	}
	//>
	//}}}
	//{{{ __toString()
	//<[__toString()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function __toString()
	{

		return $this->_str_file_path;

	}
	//>
	//}}}

}
?>
