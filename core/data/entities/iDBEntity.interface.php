<?php
/**
 * Interface iDBEntity
 *
 * Las entidades que representen un campo en la DDBB
 * deben implementar esta interface
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: iDBEntity.interface.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  entities
 * @interface
 */
interface iDBEntity
{

	public function quote();
	public function can_be_deleted();

}
?>
