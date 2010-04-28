<?php
/**
 * Class DataIterator
 * 
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: DataIterator.class.php 28 2009-05-14 14:56:00Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  data
 * @abstract
 */
abstract class DataIterator implements Iterator, Countable
{

	/**
	 * @var     array
	 * @access  protected
	 */
	protected $_data = array();

	/**
	 * @var     int
	 * @access  protected
	 */
	protected $_key;

	// -------------
	// BEGIN METHODS
	// -------------

	/**
	 * @access  public
	 * @final
	 * @param   Object  $obj  El objeto a almacenar
	 * @return  void
	 */
	public final function add($obj)
	{
		$this->_data []= $obj;
	}

	/**
	 * @access  public
	 * @final
	 * @return  void
	 */
	public final function reset()
	{
		$this->_data = array();
		$this->_key = 0;
	}

	/**
	 * @access  public
	 * @final
	 * @return  int
	 */
	public final function len()
	{
		return count($this->_data);
	}

	/**
	 * @access  public
	 * @final
	 * @return  void
	 */
	public final function rewind()
	{
		$this->_key = 0;
	}

	/**
	 * @access  public
	 * @final
	 * @return  int
	 */
	public final function key()
	{
		return $this->_key;
	}

	/**
	 * @access  public
	 * @final
	 * @return  Object
	 */
	public final function current()
	{
		return $this->_data[$this->_key];
	}

	/**
	 * @access  public
	 * @final
	 * @return  void
	 */
	public final function next()
	{
		$this->_key++;
	}

	/**
	 * @access  public
	 * @final
	 * @return  int
	 */
	public final function valid()
	{
		return $this->_key < $this->len();
	}

	/**
	 * @access  public
	 * @final
	 * @return  int
	 */
	public final function count()
	{
		return count($this->_data);
	}

	/**
	 * @access  public
	 * @return  mixed
	 */
	public function get($key=0)
	{
		return $this->_data[$key];
	}

}

?>
