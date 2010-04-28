<?php
/**
 * Class Permission
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Permission.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  core
 */
class Permission
{

	//{{{ Members
	/**
	 * Nombre del grupo
	 *
	 * @var	   int
	 * @access  private
	 */
	private $_group_name;

	/**
	 * ID del grupo
	 *
	 * @var	   int
	 * @access  private
	 */
	private $_group_id;

	/**
	 * Nombre del nivel
	 *
	 * @var	   int
	 * @access  private
	 */
	private $_level_name;

	/**
	 * ID del nivel
	 *
	 * @var	   int
	 * @access  private
	 */
	private $_level_id;
	//}}}

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct($group, $level)
	//<[__construct()]
	/**
	 * @access  public
	 * @param   string  $permission  formas:
	 *                               radar, EDIT
	 *                               turismo, 11
	 *                               2, 11
	 *                               1, EDIT
	 * @return  void
	 */
	public function __construct($group, $level)
	{

		// 
		if ( is_numeric($group) ) { 
			$this->_group_id   = $group;
			$this->_group_name = Config::get_group_name($group);
		}
		else
		{
			$this->_group_id   = Config::get_group_id($group);
			$this->_group_name = $group;
		}

		if ( is_numeric($level) )
		{
			$this->_level_id   = $level;
			$this->_level_name = Config::get_level_name($level);
		}
		else
		{
			$this->_level_id   = Config::get_level_id($level);
			$this->_level_name = $level;
		}

	}
	//>
	//}}}
	//{{{ get_group_name()
	//<[get_group_name()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_group_name()
	{

		return $this->_group_name;

	}
	//>
	//}}}
	//{{{ get_level_name()
	//<[get_level_name()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_level_name()
	{

		return $this->_level_name;

	}
	//>
	//}}}
	//{{{ get_group_id()
	//<[get_group_id()]
	/**
	 * @access  public
	 * @return  int
	 */
	public function get_group_id()
	{

		return $this->_group_id;

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

		return $this->_level_id;

	}
	//>
	//}}}
	//{{{ get_group()
	//<[get_group()]
	/**
	 * @access  public
	 * @return  int
	 */
	public function get_group()
	{

		return $this->_group_id;

	}
	//>
	//}}}
	//{{{ get_level()
	//<[get_level()]
	/**
	 * @access  public
	 * @return  int
	 */
	public function get_level()
	{

		return $this->_level_id;

	}
	//>
	//}}}
	//{{{ __toString()
	//<[__toString()]
	/**
	 * @method
	 * @access  public
	 * @return  string
	 */
	public function __toString()
	{

		return $this->_level_name . '@' . $this->_group_name;

	}
	//>
	//}}}

}
?>
