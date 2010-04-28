<?php
import('core.data.DataIterator');
import('core.data.entities.iDBEntity');
import('core.data.DBObject');

/**
 * Class DBEntity
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: DBEntity.class.php 80 2009-07-06 15:54:55Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  entities
 * @abstract
 */

abstract class DBEntity extends DataIterator implements iDBEntity
{

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ get()
	//<[get()]
	/**
	 * @access  public
	 * @param   string   $name
	 * @return  DBObject
	 */
	public function get($name)
	{

		if ( $key = $this->_key($name) )
		{
			return $this->_data[$key];
		}

	}
	//>
	//}}}
	//{{{ set($name, $value=null)
	//<[set()]
	/**
	 * @access  public
	 * @param   string   $name
	 * @param   mixed    $value
	 * @return  bool
	 */
	public function set($name, $value=null)
	{

		// Datos pasados en un array
		if ( is_array($name) )
			return $this->_set($name);

		// Dato unico
		if ( $key = $this->_key($name) )
			return $this->_data[$key]->set($value);
		else
			return false;

	}
	//>
	//}}}
	//{{{ set_from_db($id)
	//<[set_from_db()]
	/**
	 * @access  public
	 * @param   int     $id
	 * @return  bool
	 */
	public function set_from_db($id)
	{

		if ( ! is_numeric($id) && strlen($id) > 39 )
			return false;

		if ( is_string($id) )
			$id = "'" . $id . "'";

		$this->__init();

		$query  = "SELECT * FROM " . $this->table() . " WHERE ";
		$query .= $this->pkey() . "=" . $id;

		// control
		#TPL::show($query);

		$row = DB::query_row($query);

		if ($row instanceof Error)
		{
			$row->set_message('_errorDBSelect');
			$row->death($this);
		}

		return $this->set($row);

	}
	//>
	//}}}
	//{{{ html($name)
	//<[html()]
	/**
	 * @access  public
	 * @param   string   $name
	 * @return  string
	 */
	public function html($name)
	{

		if ( $key = $this->_key($name) )
			return $this->_data[$key]->html();
		else
			return null;

	}
	//>
	//}}}
	//{{{ table()
	//<[table()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function table()
	{

		return $this->_table;

	}
	//>
	//}}}
	//{{{ pkey()
	//<[pkey()]
	/**
	 * @todo esto sirve solamente para tablas con una sola pkey
	 *
	 * @access  public
	 * @return  string
	 */
	public function pkey()
	{

		foreach ( $this->_data as $dbObj )
			if ( $dbObj->pkey )
				return $dbObj->name;

		return null;

	}
	//>
	//}}}
	//{{{ id()
	//<[id()]
	/**
	 * @todo esto sirve solamente para tablas con una sola pkey
	 *
	 * @access  public
	 * @return  int
	 */
	public function id()
	{

		foreach ( $this->_data as $dbObj )
		{
			if ( $dbObj->pkey )
			{
				if ( is_numeric($dbObj->value) )
					return $dbObj->value;
				else
					return "'{$dbObj->value}'";
			}
		}

		return null;

	}
	//>
	//}}}
	//{{{ get_id()
	//<[get_id()]
	/**
	 * @todo esto sirve solamente para tablas con una sola pkey
	 *
	 * @access  public
	 * @return  int
	 */
	public function get_id()
	{
		return $this->id();
	}
	//>
	//}}}
	//{{{ check()
	//<[check()]
	/**
	 * Metodo que controla que existan los datos necesarios
	 * para realizar un insert o update.
	 *
	 * @access  public
	 * @return  bool
	 */
	public function check()
	{

		foreach ( $this->_data as $dbObj )
		{

			if ( ! $dbObj->exclude && ! $dbObj->null && 
				  ($dbObj->value === null) && ($dbObj->default === false) ) {
				// control
				#TPL::show(Utils::dump($dbObj));
				$error = new Error("DBEntity::check()");
				$error->set_description("Field '{$dbObj->name}' error");
				$error->death($this);
			}

		}

		return true;

	}
	//>
	//}}}
	//{{{ maybe_serialize($data)
	//<[maybe_serialize()]
	/**
	 * Serialize data, if needed.
	 *
	 * @access  public
	 * @param   mixed  $data  Data that might be serialized.
	 * @return  mixed         A scalar data
	 */
	public function maybe_serialize($data)
	{

		if (is_array($data) || is_object($data))
			return serialize($data);

		if ($this->is_serialized($data))
			return serialize($data);

		return $data;
	}
	//>
	//}}}
	//{{{ maybe_unserialize($original)
	//<[maybe_unserialize()]
	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @access  public
	 * @param   string  $original  Maybe unserialized original, if is needed.
	 * @return  mixed              Unserialized data can be any type.
	 */
	public function maybe_unserialize($original)
	{

		// don't attempt to unserialize data that wasn't serialized going in
		if ($this->is_serialized($original))
		{
			$original = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $original);
			if (false !== $gm = @unserialize($original))
			{
				return $gm;
			}
		}
		return $original;

	}
	//>
	//}}}
	//{{{ is_serialized($data)
	//<[is_serialized()]
	/**
	 * @access  public
	 * @param   mixed   $data
	 * @return  bool
	 */
	public function is_serialized($data)
	{

		// if it isn't a string, it isn't serialized
		if ( !is_string( $data ) )
		{
			return false;
		}

		$data = trim($data);

		if ('N;' == $data) return true;

		if (!preg_match('/^([adObis]):/', $data, $badions)) return false;

		switch ( $badions[1] )
		{
			case 'a' :
			case 'O' :
			case 's' :
				if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
				{
					return true;
				}
				break;
			case 'b' :
			case 'i' :
			case 'd' :
				if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
					return true;
				break;
		}

		return false;

	}
	//>
	//}}}
	//{{{ set_item_users($reload=false)
	//<[set_item_users()]
	/**
	 * @access  public
	 * @return  bool
	 */
	public function set_item_users($reload=false)
	{
		if (isset($this->item_users))
		{
			if ($reload || ! $this->item_users)
				$this->item_users = new ItemUsers($this->_table, $this->id());
			
			return $this->item_users;
		}
		
		return false;
	}
	//>
	//}}}
	//{{{ get_item_users()
	//<[get_item_users()]
	/**
	 * @access  public
	 * @return  ItemUsers
	 */
	public function get_item_users()
	{
		return $this->set_item_users();
	}
	//>
	//}}}
	//{{{ _set($array)
	//<[_set()]
	/**
	 * Setea el/los objeto/s desde un array
	 *
	 * @access  protected
	 * @param   array      $array
	 * @return  bool
	 */
	protected function _set($array)
	{

		if ( empty($array) )
			return false;

		// control
		#TPL::show(Utils::dump($array));

		foreach ($array as $name => $value)
		{

			$key = $this->_key($name);

			if ( $key !== null )
			{

				// Si viene de $_POST
				// controlo magic_quotes
				//
				// @fixme con esto no aseguro que el array pasado
				//        sea $_POST o provenga de el.
				//        Solo confirma que haya elementos en POST!
				if ( count($_POST) )
					#echo "$name $value <br>";
					$true = $this->_data[$key]->set($value, true);
				else
					$true = $this->_data[$key]->set($value);

				// La asignacion fue exitosa?
				if ( ! $true )
					#die("$name => $value");
					return false;

			}

		}

		// ****************
		// Seteo item_users
		// ****************
		$this->set_item_users($reload=true);

		//
		return true;

	}
	//>
	//}}}
	//{{{ _key($name)
	//<[_key()]
	/**
	 * @access  protected
	 * @param   string   $name
	 * @return  mixed
	 */
	protected function _key($name)
	{

		for ($i=0; $i<count($this->_data); $i++)
			if ($this->_data[$i]->name == $name)
				return $i;

		return null;

	}
	//>
	//}}}
	//{{{ __init()
	//<[__init()]
	/**
	 * @access  protected
	 * @return  void
	 */
	protected function __init()
	{

		// debe ser implementada en cada objeto
		// derivado de la clase DBEntity

	}
	//>
	//}}}
	//{{{ __get($key)
	//<[__get()]
	/**
	 * @access  public
	 * @param   string  $key
	 * @return  mixed
	 */
	public function __get($key)
	{

		// Caso especial: regresa el ID del objeto
		if ( $key == 'id' )
			return $this->id();

		// Las propiedades que coienzan con _
		// devuelven el valor del objeto al que apuntan.
		// Es en realidad un atajo. Ej:
		// $obj->_id_manufacturer === $obj->id_manufacturer->id_manufacturer
		$_ = false;
		if ( preg_match('/^_/', $key) )
		{
			$_ = true;
			$key  = substr($key, 1);
		}

		// Las propiedades que comienzan con form_
		// devuelven el valor convirtiendo comillas,
		// acentos y enies a sus entidades HTML
		// para poder ser usados en formularios.
		$form = false;
		if ( preg_match('/^form_/', $key) )
		{
			$form = true;
			$key  = substr($key, 5);
		}

		// Las propiedades que comienzan con html_
		// devuelven su valor convirtiendo segun
		// corresponda: html o txt (nl2br)
		$html = false;
		if ( preg_match('/^html_/', $key) )
		{
			$html = true;
			$key  = substr($key, 5);
		}

		foreach ( $this->_data as $dbObject )
		{

			if ($dbObject->name == $key)
			{

				if ($form)
					return $dbObject->form();

				if ($html)
					return $dbObject->html();

				if ($_ && is_object($dbObject->value))
					return $dbObject->value->$key;
				
				if ($dbObject->value == null && $dbObject->default)
					// default
					return $dbObject->default;
				else
					// el valor 
					return $dbObject->value;

			}

		}

	}
	//>
	//}}}
	//{{{ __set($key, $value)
	//<[__set()]
	/**
	 * @access  public
	 * @param   string  $key
	 * @return  void
	 */
	public function __set($key, $value)
	{

		foreach ( $this->_data as $DBObject )
			if ($DBObject->name == $key)
			{
				$DBObject->value = $value;
				break;
			}

	}
	//>
	//}}}
	//{{{ __call($name, $params)
	//<[__call()]
	/**
	 * @access  public
	 * @param   string  $name    Nombre del metodo
	 * @param   array   $params  Parametros pasados al metodo
	 * @return  mixed
	 */
	public function __call($name, $params)
	{

		// Devuelvo el objeto DBObject
		// si no hay parametros
		if ( empty($params) )
			foreach ( $this->_data as $DBObject )
				if ($DBObject->name == $name)
					return $DBObject;

		return false;

	}
	//>
	//}}}

	// ------------------------
	// INTERFACE IMPLEMENTATION
	// ------------------------

	//{{{ quote($name=null)
	//<[quote()]
	/**
	 * @access  public
	 * @param   string   $name
	 * @return  string
	 */
	public function quote($name=null)
	{

		if ( $name === null )
		{
			return $this->_data[$this->_key($this->pkey())]->quote();
		}

		if ( $key = $this->_key($name) )
		{
			return $this->_data[$key]->quote();
		}

	}
	//>
	//}}}
	//{{{ can_be_deleted()
	//<[can_be_deleted()]
	/**
	 * @access  public
	 * @return  bool
	 */
	public function can_be_deleted()
	{
		return true;
	}
	//>
	//}}}

}
?>
