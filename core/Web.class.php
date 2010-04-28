<?php
/**
 * Class Web
 *
 * Contiene metodos basicos para el manejo de la interface web
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Web.class.php 98 2009-11-21 21:43:24Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  core
 * @abstract
 */
abstract class Web extends ControllerBase
{

	/**
	 * URLs de las secciones del admin
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $_data = array();

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct($filePath=null)
	//<[__construct()]
	/**
	 * @access  public
	 * @param   string  $filePath  La ruta completa al archivo
	 * @return  void
	 */
	public function __construct($filePath)
	{

		// Llamada al constructor de ControllerBase
		parent::__construct($filePath);

		// Seteos TPL
		$this->_set_tpl_basics();

	}
	//>
	//}}}
	//{{{ _set_tpl_basics()
	//<[_set_tpl_basics()]
	/**
	 * Seteos base para cualquier vista
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function _set_tpl_basics()
	{
		$this->tpl->add_css('base');
		$this->tpl->add_css('main');
		$this->tpl->add_css('sidebar');
		$this->tpl->add_css('print', 'print');

		$this->tpl->add_js('base');

		$this->tpl->set_template_base('core.base');
	}
	//>
	//}}}

}
?>
