<?php
/**
 * Class Controller cIndex
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: cIndex.class.php 137 2010-03-07 21:06:23Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     controller
 */
class cIndex extends Web
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

		Router::redir('/playground');

	}
	//>
	//}}}

}
?>
