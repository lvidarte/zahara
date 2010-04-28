<?php
/**
 * Class DataCountIterator
 * 
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: DataCountIterator.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  data
 * @abstract
 */
abstract class DataCountIterator implements Iterator
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
	 * @param   Object  $obj   El objeto a almacenar
	 * @param   int     $cant  Total de objetos
	 * @return  void
	 */
	public final function add($obj, $cant=1)
	{

		$key = $this->exists($obj);

		if ( $key >= 0  )
		{
			$this->_data[$key]['total'] += $cant;
		}
		else
		{
			$this->_data []= array('item'=>$obj, 'total'=>$cant);
		}

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
	 * @return  int
	 */
	public final function valid()
	{
		return $this->_key < $this->len();
	}

	/**
	 * @access  public
	 * @return  int      -1 si no existe el elemento
	 */
	public final function exists($obj)
	{

		for ($i=0; $i<count($this->_data); $i++)
		{
			if ( $this->_data[$i]['item'] == $obj )
			{
				return $i;
			}
		}

		return -1;

	}

}

?>
