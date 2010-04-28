<?php
/**
 * Class Error
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Error.class.php 140 2010-03-08 03:02:33Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 */
class Error
{

	//{{{ Properties
	/**
	 * Referencia al objeto TPL
	 *
	 * @var	   TPL
	 * @access  private
	 */
	private $tpl = null;

	/**
	 * Almacena el mensaje de error.
	 *
	 * @var	   string
	 * @access  private
	 */
	private $_message = null;

	/**
	 * Almacena la descripcion del mensaje.
	 *
	 * @var	   string
	 * @access  private
	 */
	private $_description = null;

	/**
	 * Almacena el codigo numerico del error.
	 *
	 * @var	   int
	 * @access  private
	 */
	private $_code = 0;

	/**
	 * Se borra el contenido previo del objeto TPL?
	 *
	 * @var	   bool
	 * @access  private
	 */
	private $_clear_tpl = true;

	/**
	 * Se muestra un backtrace del error?
	 *
	 * Esto, al igual que el verdadero mensaje de error,
	 * solo podra ser visto por usuarios admin.
	 *
	 * @var	   bool
	 * @access  private
	 */
	private $_backtrace = true;

	/**
	 * Caracteres a reemplazar por su entidad HTML
	 * al momento de generar el back trace.
	 *
	 * @var	   array
	 * @access  private
	 */
	private $_replaces = array('<','>');
	//}}}

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct($_message=null, $_description=null)
	//<[__construct()]
	/**
	 * @access  public
	 * @param   string   $_message      El mensaje de error
	 * @param   string   $_description  La descripcion del error
	 * @return  void
	 */
	public function __construct($_message=null, $_description=null)
	{

		// Traigo el objeto TPL
		$this->tpl = View::tpl();

		// Hay mensaje?
		if ( $_message != null )
		{
			$this->set_message($_message);
		}
		else
		{
			$this->set_message('_404_file_not_found');
		}

		// Hay descripcion?
		if ( $_description != null )
		{
			$this->set_description($_description);
		}
		else
		{
			$this->set_description(array('_404_file_not_found_description', Router::get_url()));
		}

	}
	//>
	//}}}
	//{{{ set_message($_message)
	//<[set_message()]
	/**
	 * @access  public
	 * @param   array|string   $_message   El mensaje de error
	 * @return  void
	 */
	public function set_message($_message)
	{

		$this->_message = $this->tpl->sprintf($_message);

	}
	//>
	//}}}
	//{{{ set_description($_description)
	//<[set_description()]
	/**
	 * @access  public
	 * @param   array|string   $_description   La descripcion del error
	 * @return  void
	 */
	public function set_description($_description)
	{

		$this->_description = $this->tpl->sprintf($_description);

	}
	//>
	//}}}
	//{{{ set_code($code)
	//<[set_code()]
	/**
	 * @access  public
	 * @param   int     Numero del error
	 * @return  void
	 */
	public function set_code($code)
	{

		if ( is_string($code) )
		{
			$this->_code = $code;
			return true;
		}
		else
		{
			return false;
		}

	}
	//>
	//}}}
	//{{{ get_message()
	//<[get_message()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_message()
	{

		#return preg_replace('/\n+/','<br />',$this->_message);
		return $this->_message;

	}
	//>
	//}}}
	//{{{ get_description()
	//<[get_description()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_description()
	{

		#return nl2br($this->_description);
		return $this->_description;

	}
	//>
	//}}}
	//{{{ death($backtrace=true)
	//<[death()]
	/**
	 * 404 - File Not Found
	 *
	 * @access  public
	 * @param   Object  $obj    Objeto del cual mostrar un dump
	 * @param   bool    $die    Indica si se termina el programa
	 * @return  void
	 */
	public function death($obj=null, $die=true)
	{

		if ( $this->_clear_tpl )
		{
			$this->tpl->clear_buffer();
		}
		$this->tpl->set_template_base(Config::tplBaseDefault);
		
		// Si no hay un usuario logueado
		// se muestra el mensaje y la descripcion por default.
		// Tampoco se realiza un backtrace del programa y
		// El programa termina obligatoriamente.
		if ( ! Registry::exists('__USER__')  )
		{

			$obj = null;
			$this->set_message('_404_file_not_found');
			$this->set_description(array('_404_file_not_found_description', Router::get_url()));
			$this->set_backtrace(false);

		}
		else
		{
			$user = Registry::get('__USER__');
			$this->tpl->assign('user', $user);

			// Dump + backtrace
			// Solo para usuario ADMIN 
			if ($user->has('U_ADMIN'))
			{
				if ($obj) $this->tpl->assign('dump', Utils::dump($obj));
				$this->tpl->assign('backtrace', $this->backtrace());
			}
			
			$this->tpl->assign('code', $this->_code);

		}
		
		// ---
		// TPL
		// ---

		// Asigno message y description
		$this->tpl->assign('___404_message', $this->get_message());
		$this->tpl->assign('___404_description', $this->get_description());

		// Titulo
		$this->tpl->assign('page_title', $this->get_message());

		// Proceso el template
		$this->tpl->parse('core.404');

		// Die?
		if ($die)
		{
			// Recordar que show() muestra el tpl almacenado
			// y finaliza el programa.
			header('HTTP/1.0 404 Not Found');
			$this->tpl->show();
		}

	}
	//>
	//}}}
	//{{{ clear_tpl($on=true)
	//<[clear_tpl()]
	/**
	 * Setea el estado de $_clear_tpl
	 *
	 * @access  public
	 * @param   bool     $on    Se borra el contenido del TPL?
	 * @return  void
	 */
	public function clear_tpl($on=true)
	{

		$this->_clear_tpl = $on;

	}
	//>
	//}}}
	//{{{ set_backtrace($on=true, $replaces=null)
	//<[set_backtrace()]
	/**
	 * Setea el estado de $_backtrace
	 *
	 * backtrace(true, array('<'=>
	 *
	 * @access  public
	 * @static
	 * @param   bool   $on        Se muestra el back trace?
	 * @param   array  $replaces  Array con caracteres a reemplazar
	 * @return  mixed
	 */
	public function set_backtrace($on=true, $replaces=null)
	{

		$this->_backtrace = $on;

		// Conversion?
		if ( $this->_backtrace && is_array($replaces) )
		{
			$this->_replaces = $replaces;
		}

	}
	//>
	//}}}
	//{{{ backtrace()
	//<[backtrace()]
	/**
	 * Retorna el backtrace con los reemplazos
	 * correspondientes, si los hubiera.
	 *
	 * @access  public
	 * @return  string
	 */
	public function backtrace()
	{

		// No se muestra?
		if ( ! $this->_backtrace )
		{
			return null;
		}

		$backtrace = debug_backtrace();

		// Conversion?
		if ( is_array($this->_replaces) )
		{
			$this->replace($backtrace);
			#print_r($backtrace);die();
		}

		#print_r($this->_replaces);die();
		return print_r($backtrace, true);

	}
	//>
	//}}}
	//{{{ replace(&$_array)
	//<[replace()]
	/**
	 * Recorre recursivamente un array y
	 * reemplaza los caracteres,
	 * seteados en $this->_replaces,
	 * por sus entidades HTML.
	 *
	 * @access  private
	 * @param   array   $_array   El array a modificar
	 * @return  mixed
	 */
	private function replace(&$_array)
	{

		#return;

		if ( is_array($_array) )
		{

			foreach ($_array as $key => $value)
			{
				$this->replace($_array[$key]);
			}

		}
		elseif ( is_string($_array) )
		{

			foreach ( $this->_replaces as $replace )
			{
				$_array = str_replace($replace, htmlentities($replace), $_array);
			}

		}
		elseif ( is_object($_array) )
		{
			
			// No hay otra forma de obtener el
			// var_dump de un objeto.
			ob_start();
			var_dump($_array);
			$_array = ob_get_contents();
			ob_end_clean();

			foreach ( $this->_replaces as $replace )
			{
				$_array = str_replace($replace, htmlentities($replace), $_array);
			}


		}


	}
	//>
	//}}}

}
?>
