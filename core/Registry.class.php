<?php
/**
 * Class Registry
 *
 * Esta clase almacena objetos a traves de la aplicacion.
 *
 * @author      Leonardo Vidarte <lvidarte@gmail.com>
 * @version     $Id: Registry.class.php 28 2009-05-14 14:56:00Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  core
 * @abstract
 */
abstract class Registry
{

	/**
	 * Array con los objetos almacenados
	 *
	 * @access private
	 * @var array
	 */
	private static $_store = array();

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ set($label, $object)
	//<[set()]
	/** 
	 * @access  public
	 * @static
	 * @param   string   $label
	 * @param   string   $object
	 * @return  boolean
	 */
	public static function set($label, $object)
	{

		if (!isset(self::$_store[$label]))
		{
			self::$_store[$label] = $object;
			return true;
		}
		else
		{
			return false;
		}

	}
	//>
	//}}}
	//{{{ get($label=null)
	//<[get()]
	/** 
	 * @access  public
	 * @static
	 * @param   string  $label
	 * @return  mixed
	 */
	public static function get($label=null)
	{
		if (is_null($label))
			return self::$_store;
		elseif (isset(self::$_store[$label]))
			return self::$_store[$label];
		else
			return null;
	}
	//>
	//}}}
	//{{{ add($label, $object, $key=null)
	//<[add()]
	/** 
	 * Push one element onto the end of array
	 *
	 * @access  public
	 * @static
	 * @param   string      $label
	 * @param   mixed       $value
	 * @param   string|int  $key     index en arrays tipo hash
	 * @return  boolean
	 */
	public static function add($label, $value, $key=null)
	{
		if ( ! isset(self::$_store[$label]) )
			self::$_store[$label] = array();
		elseif ( ! is_array(self::$_store[$label]) )
			return false;

		if ($key !== null)
			self::$_store[$label][$key] = $value;
		else
			self::$_store[$label] []= $value;
		
		return true;
	}
	//>
	//}}}
	//{{{ get_by_key($label, $key)
	//<[get_by_key()]
	/** 
	 * Get element from array
	 *
	 * @access  public
	 * @static
	 * @param   string  $label
	 * @param   int     $key
	 * @return  mixed
	 */
	public static function get_by_key($label, $key)
	{

		if (isset(self::$_store[$label]) &&
			 is_array(self::$_store[$label]) &&
			 array_key_exists($key, self::$_store[$label])
			)
		{
			return self::$_store[$label][$key];
		}
		else
		{
			return null;
		}

	}
	//>
	//}}}
	//{{{ in_array($object, $label)
	//<[in_array()]
	/** 
	 * Checks if a value exists in an array
	 *
	 * @access  public
	 * @static
	 * @param   string   $object
	 * @param   string   $label
	 * @return  boolean
	 */
	public static function in_array($object, $label)
	{

		if (isset(self::$_store[$label]) &&
			 is_array(self::$_store[$label]) &&
			 in_array($object, self::$_store[$label])
			) {

			return true;
		}
		else
		{
			return false;
		}

	}
	//>
	//}}}
	//{{{ count($label)
	//<[count()]
	/** 
	 * @access  public
	 * @static
	 * @param   string  $label
	 * @return  mixed
	 */
	public static function count($label)
	{
		if (isset(self::$_store[$label]) && is_array(self::$_store[$label]))
		{
			return count(self::$_store[$label]);
		}
		else
		{
			return null;
		}
	}
	//>
	//}}}
	//{{{ unregister($label)
	//<[unregister()]
	/** 
	 * @access  public
	 * @static
	 * @param   string  $label
	 * @return  mixed
	 */
	public static function unregister($label)
	{
		unset(self::$_store[$label]);
	}
	//>
	//}}}
	//{{{ exists($label)
	//<[exists()]
	/** 
	 * @access  public
	 * @static
	 * @param   string   $label
	 * @return  boolean
	 */
	public static function exists($label)
	{
		if (isset(self::$_store[$label]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	//>
	//}}}

}
?>
