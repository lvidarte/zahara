<?php
/**
 * Smarty plugin
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: function.load.php 33 2009-05-22 03:43:37Z xleo $
 * @package     Smarty
 * @subpackage  plugins
 */

/**
 * Smarty {load} function plugin
 *
 * Type:     function<br>
 * Name:     load<br>
 * Purpose:  load some object()
 */
function smarty_function_load($params, &$smarty)
{
   switch ($params['name'])
   {
      case 'users':
	      $result = DB::get_select('users','id_user','username');
         break;
      
   }

   $smarty->assign($params['assign'], $result);
}

/* vim: set expandtab: */

?>
