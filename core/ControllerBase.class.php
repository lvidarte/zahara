<?php
import('core.TPL');
import('core.View');
import('core.Utils');

/**
 * Class ControllerBase 
 * 
 * Todas las clases controller deben extender esta clase
 *
 * @author      Leonardo Vidarte <lvidarte@gmail.com>
 * @version     $Id: ControllerBase.class.php 21 2009-05-03 02:55:02Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 * @abstract
 */
abstract class ControllerBase
{

	//{{{ Members
	/**
	 * Component
	 *
	 * Guarda un objeto de tipo FilePath
	 * cuyo valor es el path completo
	 * al archivo de la clase que fue
	 * llamada por Router y que extiende ControllerBase.
	 *
	 * @var     FilePath
	 * @access  protected
	 */
	protected $file_path = null;

	/**
	 * Contiene el objeto TPL
	 *
	 * @var     TPL
	 * @access  protected
	 */
	protected $tpl = null;

	/**
	 * Contiene el objeto HTML
	 *
	 * @var     HTML
	 * @access  protected
	 */
	protected $html = null;

	/**
	 * Contiene el objeto Session
	 *
	 * @var     Session
	 * @access  protected
	 */
	protected $session = null;

	/**
	 * Contiene el objeto User
	 *
	 * @var     User
	 * @access  protected
	 */
	protected $user = null;
	//}}}

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct($file_path)
	//<[__construct()]
	/**
	 * Las clases que exiendan ControllerBase y
	 * definan un constructor deben, como primera medida,
	 * llamar al constructor de ControllerBase.
	 *
	 * @access  public
	 * @param   string  $file_path  path al archivo controlador
	 * @return  void
	 */
	public function __construct($file_path) {

		// Creo el objeto FilePath
		$this->file_path = new FilePath($file_path);

		// Traigo el objeto TPL
		$this->tpl = View::tpl();

		// Creo el objeto HTML
		$this->html = new HTML;

		// Traigo Session
		if (Registry::exists('__SESSION__'))
			$this->session = Registry::get('__SESSION__');

		// Traigo User
		if (Registry::exists('__USER__'))
			$this->user = Registry::get('__USER__');

		// Seteo $user en el tpl
		// esto setea tambien el usuario
		// de la subclase Admin
	   $this->load_user();

	}
	//>
	//}}}
	//{{{ check_params(&$params, $types, $die=true, $strict=false)
	//<[check_params()]
	/**
	 * Metodo que chequea el tipo de los parametros pasados por URL.
	 * Se aceptan menos parametros que tipos (con $strict=true),
	 * pero no al reves.
	 *
	 * Formato string de tipos:
	 *   s|i|r[:condicion][;s|i|r[:condicion]]..
	 *
	 * Ejemplo:
	 *   $types = 's;s;i'; // se esperan dos strings y un entero
	 *   $types = 's:20;i:<=100'; // un string de 20 caracteres y un entero <= 100
	 *   $types = 'r:/^\w{2,10}$/'; // regex: entre 2 y 10 caracteres alfanumericos
	 *
	 * @access  protected
	 * @param   array       $params  array con parametros
	 * @param   string      $types   string con tipos (ver ejemplos de formato)
	 * @param   bool        $die     404 si encuentra errores?
	 * @param   bool        $strict  Se permiten menos parametros de los esperados?
	 * @return  bool|Error 
	 */
	protected function check_params(&$params, $types, $die=true, $strict=false) {

		// *****
		// TYPES
		// *****
		// No se admiten parametros?
		if (empty($types) && count($params)) {
			return $this->_error_handle(
				array('_error_params_strict', 0), $die
			);
		}
		// String
		elseif (is_string($types)) {
			$types = explode(';', $types);
		}
		// Otro
		else {
			// @todo ver esto...
			return false;
		}


		// ***********
		// CHECK COUNT
		// ***********
		$c_params = count($params);
		$c_types = count($types);
		//
		if ($strict && $c_params < $c_types) {
			// faltan parametros
			return $this->_error_handle(
				array('_error_params_strict', $c_types), $die
			);
		}
		elseif ($c_params <= $c_types) {
			$c = $c_params;
		}
		else {
			return $this->_error_handle(
				array('_error_params_too_many', $c_types), $die
			);
		}

		// *******************
		// CHECK TYPES/LENGTHS
		// *******************
		for($i = 0; $i < $c; $i++) {

			// Extraccion tipo-condicion
			@list($type, $condition) = explode(':', $types[$i]);
			#die(gettype($condition));

			// Busqueda operador comparacion
			if (!is_null($condition)) {
				$operator = preg_replace('/^(\<=|\>=|\<|\>|!=).+/', '\1', $condition);
				$value2 = ($operator == $condition) ? $condition : substr($condition, count($operator));
			}
			else {
				$operator = null;
				$value2 = null;
			}

			// Array mensaje generico ante posible error
			$message = array('_error_params', $i, $params[$i], $type, $condition);

			// Chequeo tipo / conversion
			switch ($type) {

				// String
				case 's':
				case 'str':
					if (!$this->_cmp(strlen($params[$i]), $value2, $operator)) {
						return $this->_error_handle($message, $die);
					}
					break;

				// Integer
				case 'i':
				case 'int':
					if (!is_numeric($params[$i])) {
						return $this->_error_handle($message, $die);
					}
					if ($params[$i] != ((int)$params[$i])) {
						return $this->_error_handle($message, $die);
					}
					$params[$i] = (int) $params[$i];
					if (!$this->_cmp($params[$i], $value2, $operator)) {
						return $this->_error_handle($message, $die);
					}
					break;

				// Float
				case 'f':
				case 'float':
					if (!is_numeric($params[$i])) {
						return $this->_error_handle($message, $die);
					}
					if ($params[$i] != ((float)$params[$i])) {
						return $this->_error_handle($message, $die);
					}
					$params[$i] = (float) $params[$i];
					if (!$this->_cmp($params[$i], $value2, $operator)) {
						return $this->_error_handle($message, $die);
					}
					break;

				// Regex
				case 'r':
				case 'regex':
					if (!preg_match($condition, $params[$i])) {
						return $this->_error_handle($message, $die);
					}
					break;

				default:
					return $this->_error_handle($message, $die);

			}

		}

		// ******
		// ALL OK
		// ******
		return true;

	}
	//>
	//}}}
	//{{{ check_params_strict(&$params, $types, $die=true)
	//<[check_params_strict()]
	/**
	 * Metodo que chequea el tipo de los parametros pasados por URL.
	 * Este metodo es estricto en cuanto a la cantidad de parametros:
	 * debe coincidir con la cantidad de tipos.
	 *
	 * Formato string de tipos:
	 *   s|i|r[:condicion][;s|i|r[:condicion]]..
	 *
	 * Ejemplo:
	 *   $types = 's;s;i'; // se esperan dos strings y un entero
	 *   $types = 's:20;i:<=100'; // un string de 20 caracteres y un entero <= 100
	 *   $types = 'r:/^\w{2,10}$/'; // regex: entre 2 y 10 caracteres alfanumericos
	 *
	 * @access  protected
	 * @param   array       $params  array con parametros
	 * @param   string      $types   string con tipos (ver ejemplos de formato)
	 * @param   bool        $die     404 si encuentra errores?
	 * @return  bool|Error 
	 */
	protected function check_params_strict(&$params, $types, $die=true) {

		return $this->check_params($params, $types, $die, true);

	}
	//>
	//}}}
	//{{{ check_referer($allowed=array())
	//<[check_referer($allowed)]
	/**
	 * @access  public
	 * @param   array   $allowed
	 * @return  bool
	 */
	public function check_referer($allowed=array()) {

		if (isset($_SERVER['HTTP_REFERER']))
		{
			$host = 'http://' . $_SERVER['HTTP_HOST'];

			// Conversion array
			if (!is_array($allowed))
				$allowed = array($allowed);

			// URL del controlador que llamo a check_referer
			$allowed []= $this->get_url();
			#TPL::show(Utils::dump($allowed));

			$referer = (substr($_SERVER['HTTP_REFERER'], -1) == '/') ?
				substr($_SERVER['HTTP_REFERER'], 0, -1) :
				$_SERVER['HTTP_REFERER'];

			#print $ref;

			// Control
			for ($i = 0; $i < count($allowed); $i++)
			{
				$regex = $host . $allowed[$i];
				if (preg_match("#^{$regex}#", $referer))
					return true;
			}

		}
				

		$error = new Error(
			array('_404_referer_dont_match')
		);
		$error->death();

	}
	//>
	//}}}
	//{{{ get_referer()
	//<[get_referer()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_referer() {

		$referer = null;

		if (isset($_SERVER['HTTP_REFERER']))
		{
			$referer = (substr($_SERVER['HTTP_REFERER'], -1) == '/') ?
				substr($_SERVER['HTTP_REFERER'], 0, -1) :
				$_SERVER['HTTP_REFERER'];

		}
				
		return $referer;

	}
	//>
	//}}}
	//{{{ check_user_has($id_levels)
	//<[check_user_has($id_levels)]
	/**
	 * @access  public
	 * @param   string|array  $id_levels
	 * @return  bool
	 */
	public function check_user_has($id_levels) {

		if (is_string($id_levels))
			$id_levels = array($id_levels);

		for ($i = 0; $i < count($id_levels); $i++)
		{
			$user = Registry::get('__USER__');

			if ($user->has($id_levels[$i]))
				return true;
		}

		$error = new Error(
			array('_user_auth_error'),
			array(
				'_user_auth_error_description',
				$this->get_url()
			)
		);
		$error->death();

	}
	//>
	//}}}
	//{{{ get_url()
	//<[get_url()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_url() {

		return $this->file_path->get_url();

	}
	//>
	//}}}
	//{{{ get_level_id()
	//<[get_level_id()]
	/**
	 * @access  public
	 * @return  int
	 */
	public function get_level_id() {

		return $this->file_path->get_level_id();

	}
	//>
	//}}}
	//{{{ get_file_path()
	//<[get_file_path()]
	/**
	 * @access  public
	 * @return  FilePath
	 */
	public function get_file_path() {

		return $this->file_path;

	}
	//>
	//}}}
	//{{{ load_user()
	//<[load_user()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function load_user() {

		if (Registry::exists('__USER__'))
			$this->tpl->assign('user', Registry::get('__USER__'));
		else
			$this->tpl->assign('from', '?from='.$this->get_url());

	}
	//>
	//}}}
	//{{{ _cmp($value1, $value2, $operator)
	//<[_cmp()]
	/**
	 * @access  private
	 * @param   int|float  $value1
	 * @param   int|float  $value2
	 * @param   string     $operator  Operador de comparacion
	 * @return  bool
	 */
	private function _cmp($value1, $value2, $operator) {

		if (is_null($value2)) return true;

		switch ($operator) {
			case '<=' : return ($value1 <= $value2);
			case '>=' : return ($value1 >= $value2);
			case '!=' : return ($value1 != $value2);
			case '<' : return ($value1 < $value2);
			case '>' : return ($value1 > $value2);
			default : return ($value1 == $value2);
		}

	}
	//>
	//}}}
	//{{{ _error_handle($message, $die)
	//<[_error_handle()]
	/**
	 * @access  private
	 * @param   string|array  $message  Mensaje de error
	 * @param   bool          $die      404 si encuentra errores?
	 * @return  Error
	 */
	private function _error_handle($message, $die=true) {

		$error = new Error;

		// Message
		$error->set_message($message);

		// Description
		$b = debug_backtrace();
		$error->set_description(array(
			'_error_backtrace', 
			$b[2]['file'],
			$b[2]['line'],
			$b[2]['function'],
			print_r($b[2]['args'], true)
		));

		// Return OR Die
		if ($die) {
			$error->death();
		}
		else {
			return $error;
		}

	}
	//>
	//}}}

}
?>
