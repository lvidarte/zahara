<?php
/**
 * Class Session
 *
 * Esta clase almacena objetos a traves de una sesion.
 *
 * @author      Leonardo Vidarte <lvidarte@gmail.com>
 * @version     $Id: Session.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  core
 */
class Session
{

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct()
	//<[__construct()]
	/** 
    * @access  public
    * @return  void
    */
	public function __construct()
	{
		session_start();

		if (!isset($_SESSION[Config::phpSession]))
			$_SESSION[Config::phpSession] = array();

		// Referencia al array
		$this->_objs =& $_SESSION[Config::phpSession];
	}
	//>
	//}}}
	//{{{ set($label, $value)
	//<[set()]
	/** 
    * @access  public
    * @param   string   $label
    * @param   string   $value
    * @return  boolean
    */
	public function set($label, $value)
	{
		if (is_object($value))
		{
			$this->_objs[$label] = array(
				'__CLASS_NAME__' => get_class($value),
				'__OBJECT_VALUE__' => serialize($value)
			);
		}
		else
			$this->_objs[$label] = $value;
	}
	//>
	//}}}
	//{{{ get($label)
	//<[get()]
	/** 
    * @access  public
    * @param   string  $label
    * @return  mixed
    */
	public function get($label)
	{

		if ($this->exists($label))
		{

			// @fixme 
			if (is_string($this->_objs[$label]))
				return $this->_objs[$label];
			elseif (isset($this->_objs[$label]['__CLASS_NAME__']) &&
				  isset($this->_objs[$label]['__OBJECT_VALUE__']))
			{
				$obj = unserialize($this->_objs[$label]['__OBJECT_VALUE__']);
				return $obj;
			}
			else
				return $this->_objs[$label];

		}
		else
			return null;

	}
	//>
	//}}}
	//{{{ remove($label)
	//<[remove()]
	/** 
    * @access  public
    * @param   string  $label
    * @return  mixed
    */
	public function remove($label)
	{
		$aux = $this->get($label);

		if (!is_null($aux))
			$this->unregister($label);
		
		return $aux;
	}
	//>
	//}}}
	//{{{ unregister($label)
	//<[unregister()]
	/** 
    * @access  public
    * @param   string  $label
    * @return  mixed
    */
	public function unregister($label)
	{

		unset($this->_objs[$label]);

	}
	//>
	//}}}
	//{{{ exists($label)
	//<[exists()]
	/** 
    * @access  public
    * @param   string   $label
    * @return  boolean
    */
	public function exists($label)
	{

		return isset($this->_objs[$label]);

	}
	//>
	//}}}
	//{{{ destroy()
	//<[destroy()]
	/** 
    * @access  public
    * @return  boolean
    */
	public function destroy()
	{

		$_SESSION[Config::phpSession] = array();

		if (isset($_COOKIE[session_name()]))
		    setcookie(session_name(), '', time()-42000, '/');

		session_destroy();		

	}
	//>
	//}}}

}
?>
