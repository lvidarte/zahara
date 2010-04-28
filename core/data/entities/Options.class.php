<?php
import('core.data.entities.DBEntity');
import('core.data.DBObject');

/**
 * Class Options
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Options.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  entities
 */
class Options extends DBEntity
{

	/**
	 * @access  protected
	 * @var     string
	 */
	protected $_table = 'options';

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct()
	//<[__construct()]
	/**
	 * @access  public
	 * @param   array   $values
	 * @return  void
	 */
	public function __construct($values=null)
	{

		// Seteo de valores por default
		$this->__init();

		// Seteo valores pasados al constructor
		if ( $values === null )
		{
			$values = DB::query_row('SELECT * FROM options WHERE id_option=1');
		}

		$this->set($values);
	}
	//>
	//}}}
	//{{{ __init()
	//<[__init()]
	/**
	 * @access  public
	 * @return  void
	 */
	protected function __init()
	{

		$this->_data = array(

			new DBObject('id_option', 'int:10', 1, 'exclude:true', 'pkey:true'),
			new DBObject('payments_3', 'float'),
			new DBObject('payments_6', 'float'),
			new DBObject('payments_12', 'float'),
			new DBObject('suc_oca_350', 'float'),
			new DBObject('suc_oca_351', 'float'),
			new DBObject('oca_350', 'float'),
			new DBObject('oca_351', 'float'),
			new DBObject('insurance', 'float'),
			new DBObject('price_intval', 'tinyint:1')

		);

	}
	//>
	//}}}
	//{{{ update()
	//<[update()]
	/**
	 * Funcion que hace un update en la BBDD del objeto
	 *
	 * @access  public
	 * @param   bool    $true  Indica si la operacion debe realizarse o es una simulacion
	 * @return  bool
	 */
	public function update($true=true)
	{

		$this->check();

		$result = DB::update($this, $true);

		if ( $result instanceof Error )
		{
			$result->setCode('dbu07');
			$result->death($this);
		}
		
		return true;

	}
	//>
	//}}}

}
?>
