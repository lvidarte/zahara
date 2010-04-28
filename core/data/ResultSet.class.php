<?php
/**
 * Class ResultSet
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: ResultSet.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  data
 */
class ResultSet
{

	/**
	 * Almacena el resultado de la consulta SQL
	 *
	 * @var	   object   Result
	 * @access  private
	 */
	private $_rs = null;

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct(&$_rs)
	//<[__construct()]
	/**
	 * @access  public
	 * @param   object   $_rs  Result Object
	 * @return  void
	 */
	public function __construct(&$_rs)
	{

		$this->_rs = $_rs;

	}
	//>
	//}}}
	//{{{ num_rows()
	//<[num_rows()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function num_rows()
	{

		return $this->_rs->num_rows;

	}
	//>
	//}}}
	//{{{ fetch_row()
	//<[fetch_row()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function fetch_row()
	{

		return $this->_rs->fetch_assoc();

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

		$this->_message = preg_replace('/near \'/',"near\n'", $this->_message);
		return nl2br($this->_message);

	}
	//>
	//}}}

}
?>
