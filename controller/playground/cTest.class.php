<?php
/**
 * Class Controller cTest
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: cIndex.class.php 84 2009-07-13 00:40:20Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     controller
 * @subpackage  playground
 */
class cTest extends Playground
{

	//{{{ __index($params)
	/**
	 * @access  public
	 * @param   array   $params
	 * @return  void
	 */
	public function __index($params)
	{
		//<[example1]
		$this->tpl->add(
			$this->html->h2('Hello world'),
			Utils::print_code(__FILE__, 'example1', true)
		);
		//>

		// Response HTTP
		$this->tpl->show();
	}
	//}}}

}
?>
