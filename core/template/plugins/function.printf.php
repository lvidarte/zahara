<?php
/**
 * Smarty plugin
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: function.printf.php 1 2009-03-11 11:29:08Z xleo $
 * @package     Smarty
 * @subpackage  plugins
 */

/**
 * Smarty {printf} function plugin
 *
 * Type:     function<br>
 * Name:     printf<br>
 * Purpose:  print strings like function printf()
 */
function smarty_function_printf($params, &$smarty)
{
   call_user_func_array('printf', $params);
}

/* vim: set expandtab: */

?>
