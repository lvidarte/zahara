<?php
/**
 * Class Mem
 *
 * Clase para manejo memcache
 *
 * @author      Leonardo Vidarte <lvidarte@gmail.com>
 * @version     $Id: Mem.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 * @abstract
 */
abstract class Mem
{

	/**
	 * Conexion memcache
	 *
	 * @access private
	 * @var Memcache
	 */
	private static $_memcache = null;

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ set($key, $var, $flag=0, $expire=0)
	//<[set()]
	/** 
	 * @access  public
	 * @static
	 * @param   string   $key
	 * @param   mixed    $var
	 * @param   int      $flag
	 * @param   int      $expire
	 * @return  boolean
	 */
	public static function set($key, $var, $flag=0, $expire=0)
	{

		return self::connect()->set($key, $var, $flag, $expire);

	}
	//>
	//}}}
	//{{{ get($key, $flags)
	//<[get()]
	/** 
	 * @access  public
	 * @static
	 * @param   string|array  $key
	 * @param   int           $flags
	 * @return  string|array
	 */
	public static function get($key, $flags=0)
	{

		return self::connect()->get($key, $flags);

	}
	//>
	//}}}
	//{{{ connect()
	//<[connect()]
	/** 
	 * @access  public
	 * @static
	 * @return  Memcache
	 */
	public static function connect()
	{

		if ( ! self::$_memcache instanceOf Memcache )
		{
			self::$_memcache = new Memcache;
			self::$_memcache->connect('localhost', 11211)
				or die("Memcache: Could not connect.");
		}

		return self::$_memcache;

	}
	//>
	//}}}

}
?>
