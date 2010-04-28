<?php
/**
 * Class DBObject
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: DBObject.class.php 106 2009-11-29 23:01:39Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  data
 * @see         DataIterator
 */
class DBObject
{

	//{{{ Members
	/**
	 * Datos 
	 * @access  private
	 * @var     array
	 */
	protected $_data = array(
		'name'    => null,
		'type'    => null,
		'value'   => null,
		'default' => false,
		'null'    => false,
		'pkey'    => false,
		'exclude' => false
	);

	/**
	 * Mantiene el estado de la
	 * ultima accion realizada
	 *
	 * @access  private
	 * @var     bool
	 */
	private $_last_action = false;
	//}}}

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct($name, $type, $value=null)
	//<[__construct()]
	/**
	 * @access  public
	 * @param   string  $name   Nombre del campo en la BBDD 
	 * @param   string  $type   Tipo del dato
	 * @param   mixed   $value  Valor
	 * @return void
	 */
	public function __construct($name, $type, $value=null)
	{

		// Obtengo argumentos pasados a la funcion
		$args = func_get_args();

		// Obtengo cantidad de argumentos pasados
		$num  = func_num_args();

		// control
		#TPL::show(Utils::dump($args));

		// Orden de seteo: $this->type y luego $this->value
		// 
		// La idea es que $this->value se autocontrole
		// utilizando el valor de $this->type 
		//
		// Solo si existe $name y $type se toman en cuenta el resto
		// de los parametros
		if ( ($this->type = $type) && ($this->name = $name) )
		{
			
			$this->value = $value;

			// Resto de los parametros
			// los cuales son de tipo 'property:true|false'
			for ($i=3; $i<$num; $i++)
			{

				$property = explode(':', $args[$i], 2);
				$this->$property[0] = $property[1];

			}

		}

	}
	//>
	//}}}
	//{{{ set($value, $check_magic_quotes=false)
	//<[set()]
	/**
	 * @access  public
	 * @param   mixed
	 * @return  bool
	 */
	public function set($value, $check_magic_quotes=false)
	{

		if ( $value == '' )
			$this->value = null;

		elseif ( $check_magic_quotes && get_magic_quotes_gpc() )
			// Valores que vienen desde un formulario
			$this->value = stripslashes(trim($value));

		else
			// Valores que vienen desde la BBDD
			$this->value = trim($value);

		return $this->_last_action;

	}
	//>
	//}}}
	//{{{ exclude($bool=false)
	//<[exclude()]
	/**
	 * @access  public
	 * @param   bool
	 */
	public function exclude($bool=false)
	{

		if ( is_bool($bool) )
			$this->_data['exclude'] = $bool;

	}
	//>
	//}}}
	//{{{ quote()
	//<[quote()]
	/**
	 * @access  public
	 * @return  mixed
	 * @static
	 */
	public function quote()
	{

		// Si el valor no existe y es requerido,
		// se devuelve el valor por default
		if ($this->value === null)
			return ($this->null) ? 'NULL' : $this->default;

		// Esto puede dar un Notice, debido a que
		// hay etiquetas que no definen un valor, como
		// 'text', 'tinytext', 'longtext'
		@list($type, $extra) = explode(':', $this->type, 2);


		switch ($type)
		{

			case 'smallint' :
			case 'tinyint'  :
			case 'int'      :
			case 'float'    :
				return $this->value;
				break;

			case 'varchar'  :
			case 'text'     :
			case 'tinytext' :
			case 'longtext' :
			case 'enum'     :
				return "'" . DB::connect()->real_escape_string($this->value) . "'";
				break;

			case 'object' :
				// Si lo guardado en value es un objeto
				// se llama al metodo quote(), que poseen
				// todos los objetos (heredado de DBEntity),
				// que devuelve el valor que debe ser
				// ingresado en la DB
				//
				// Ej: el objeto Date devuelve algo como 
                // '2007-06-05 13:25:12' 
				if (is_object($this->value))
					return $this->value->quote();
				else
					return $this->default;
				break;

		}

		return null;

	}
	//>
	//}}}
	//{{{ html()
	//<[html()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function html()
	{

		@list($type, $extra) = explode(':', $this->type, 2);

		switch ($type)
		{

			case 'smallint' :
			case 'tinyint'  :
			case 'int'      :
			case 'float'    :
				return $this->value;
				break;

			case 'varchar' :
			case 'text'    :
				if ( preg_match('/^\{html\}/i', $this->value) )
				{
					$this->value = substr($this->value, 6);
					return $this->value;
				}
				else
				{
					return nl2br(htmlentities($this->value, ENT_QUOTES, 'UTF-8'));
				}
				break;

			case 'object' :
				return $this->value->quote();
				break;

		}

		return null;

	}
	//>
	//}}}
	//{{{ form()
	//<[form()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function form()
	{

		@list($type, $extra) = explode(':', $this->type, 2);

		switch ($type)
		{

			case 'varchar' :
			case 'text'    :
				return htmlentities($this->value, ENT_QUOTES, 'UTF-8');
				break;

		}

		return null;

	}
	//>
	//}}}
	//{{{ _check_type()
	//<[_check_type()]
	/**
	 * @access  protected
	 * @param   string     $value
	 * @return  mixed
	 */
	public function _check_type($value)
	{

		// Esto puede dar un Notice, debido a que
		// hay etiquetas que no definen un valor, como
		// 'text', 'tinytext', 'longtext'
		@list($type, $extra) = explode(':', $value, 2);

		// Control de syntaxis
		if ( ! isset($type) )
			return false;

		switch ($type)
		{

			case 'tinyint'  :
			case 'smallint' :
			case 'int'      :
			case 'varchar'  :
				if ( ! isset($extra) || ! is_numeric($extra) )
					return false;
				break;

			case 'tinytext' : #255
			case 'text'     : #65535
			case 'longtext' : #4,294,967,295
			case 'float'    :
				break;

			case 'enum' :
				if ( ! isset($extra) )
					return false;
				break;

			case 'object' :
				if ( ! isset($extra) || ! is_string($extra) )
					return false;
				break;

			default : return false;

		}

		return true;

	}
	//>
	//}}}
	//{{{ _check_value()
	//<[_check_value()]
	/**
	 * @access  protected
	 * @param   string     $value   Notar que $value es pasado por referencia
	 *                              para poder ser modificado en caso que el largo
	 *                              de type haya sido superado:
	 *
	 *                              si 'varchar:8'
	 *                                'hola mundo' debe recortarse a 'hola mun'
	 * @return  mixed
	 */
	public function _check_value( & $value )
	{

		if ($value === null)
			return true;

		// Esto puede dar un Notice, debido a que
		// hay etiquetas que no definen un valor extra,
		// como 'text', 'tinytext', 'longtext'...
		@list($type, $extra) = explode(':', $this->type, 2);

		switch ($type)
		{

			case 'smallint' :
			case 'tinyint'  :
			case 'int'      :
			case 'float'    :
				if ( $value == '' )
				{
					// modificacion $value
					$value = null;
					return true;
				}
				if ( ! is_numeric($value) )
					return false;
				break;

			case 'varchar' :
				if ( ! is_string($value) )
					return false;
				elseif ( strlen($value) > $extra )
					// modificacion $value
					$value = substr($value, 0, $extra);
				break;

			case 'tinytext' : #255
			case 'text'     : #65535
			case 'longtext' : #4,294,967,295
				// @todo controlar length
				return true;
				break;

			case 'enum' :
				return true;
				break;

			case 'object' :
				if ( ! $value instanceof $extra )
				{
					// Trato de crear el objeto
					if ( $extra == 'Date' )
					{
						// Modificacion $value
						$value = new Date($value);
					}
					elseif ( is_numeric($value) )
					{
						// No tiene sentido setear un objeto
						// cuyo ID sea menor o igual a cero
						if ( $value > 0 )
						{
							$obj = new $extra;
							$obj->set_from_db($value);
							// modificacion $value
							$value = $obj;
						}
					}
					else
					{
						return false;
					}
				}
				break;

			//
			default : return false;

		}

		return true;

	}
	//>
	//}}}
	//{{{ __get()
	//<[__get()]
	/**
	 * @access  public
	 * @param   string  $key
	 * @return  mixed
	 */
	public function __get($key)
	{

		if ( $key == 'quote' )
		{
			return $this->quote();
		}

		if ( $key == 'form' )
		{
			return $this->form();
		}

		if ( $key == 'html' )
		{
			return $this->html();
		}

		if ( array_key_exists($key, $this->_data) )
		{
			return $this->_data[$key];
		}

	}
	//>
	//}}}
	//{{{ __set()
	//<[__set()]
	/**
	 * @access  public
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  bool
	 */
	public function __set($key, $value)
	{

		// Primer control
		if ( ! array_key_exists($key, $this->_data) )
		{
			$this->_last_action = false;
			return;
		}

		// Segundo control: existe type?
		//
		// Como la primer propiedad que se
		// inicializa es $this->type...
		//
		// ...si no existe $this->type y el valor pasado
		// no corresponde con el formato requerido por $type
		// entonces $type no es type, sino otra cosa.
		if ( ! $this->type && ! $this->_check_type($value) )
		{

			// retorno false y esto hace que el resto
			// de los parametros pasados a la funcion
			// sean ignorados
			$this->_last_action = false;
			return;
		}

		switch ($key)
		{

			case 'name' :
				if ( ! is_string($value) )
				{
					$this->_last_action = false;
					return;
				}
				break;

			case 'type' :
				if ( ! $this->_check_type($value) )
				{
					$this->_last_action = false;
					return;
				}
				break;

			case 'value' :
				if ( ! $this->_check_value($value) )
				{
					$this->_last_action = false;
					return;
				}
				break;

			case 'default' :
				if ( is_numeric($value) )
				{
					$value = (int) $value;
				}
				else
				{
					switch ($value)
					{
						case 'null'  : $value = null; break;
						case 'false' : $value = false; break;
						default      : $this->_last_action = false; return;
					}
				}
				break;

			case 'null'    :
			case 'exclude' :
			case 'pkey'    :
				switch ($value)
				{
					case 'true'  : $value = true; break;
					case 'false' : $value = false; break;
					default      : $this->_last_action = false; return;
				}
				break;

			default :
				$this->_last_action = false;
				return;

		}

		$this->_data[$key] = $value;

		$this->_last_action = true;

	}
	//>
	//}}}

}
?>
