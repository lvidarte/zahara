<?php
import('core.template.TPL');

/**
 * MVC VIEW class
 *
 * De momento solo es un Singleton para el objeto TPL
 *
 * @author    Leonardo Vidarte <lvidarte@gmail.com>
 * @version   $Id: View.class.php 118 2009-11-30 05:06:27Z xleo $
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package   view
 */
abstract class View
{

	/**
	 * Component
	 * @var      object Smarty
	 * @access   private
	 */
	private static $tpl = null;

	//{{{ tpl()
	//<[tpl()]
	/**
	 * Singleton pattern
	 *
	 * @access  public
	 * @return  object TPL
	 */
	public static function tpl()
	{
		return self::get_tpl();
	}
	//>
	//}}}
	//{{{ get_tpl()
	//<[get_tpl()]
	/**
	 * Singleton pattern
	 *
	 * @access  public
	 * @return  object TPL
	 */
	public static function get_tpl()
	{
		if (!self::$tpl)
			self::$tpl = new TPL;	

		return self::$tpl;
	}
	//>
	//}}}

}
