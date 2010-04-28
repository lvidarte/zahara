<?php
/**
 * Class Controller cIndex
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: cIndex.class.php 84 2009-07-13 00:40:20Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     controller
 * @subpackage  playground
 */
class cIndex extends Playground
{

	//{{{ __index($params)
	//<[__index]
	/**
	 * @access  public
	 * @param   array   $params
	 * @return  void
	 */
	public function __index($params)
	{
		$this->tpl->add(
			$this->html->h2('Hello world')
		);

		// Response HTTP
		$this->tpl->show();
	}
	//>
	//}}}
	//{{{ info()
	//<[info]
	/**
	 * @access  public
	 * @param   array   $params
	 * @return  void
	 */
	public function info($params)
	{
		phpinfo();
		exit;
	}
	//>
	//}}}

}
?>
