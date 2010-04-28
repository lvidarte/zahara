<?php
/**
 * Interface iFile
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: iFile.interface.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  entities
 * @interface
 */
interface iFile
{

	public function setImageSize($width, $height=0);
	public function setThumb($bool);
	public function setWaterMark($bool);
	public function setWaterMarkType($type);

}
?>
