<?php
/**
 * Config file for MVC application
 *
 * @author   Leonardo Vidarte <lvidarte@gmail.com>
 * @version  $Id: Config.class.php 140 2010-03-08 03:02:33Z xleo $
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package  config
 */
abstract class Config
{

	// Application
	const appName              = "Zahara";
	const appDescription       = "Mini MVC-Framework PHP/MySQL";
	const appVersion           = "0.3.0";
	const appBaseDir           = APP_BASE_DIR;
	const appPublicBaseDir     = APP_PUBLIC_BASE_DIR;
	const appDirSep            = "/";
	const appConfigBaseDir     = "/config";
	const appCoreBaseDir       = "/core";
	const appModelBaseDir      = "/model";
	const appViewBaseDir       = "/view";
	const appControllerBaseDir = "/controller";
	const appUploadsBaseDir    = "/uploads";
	const appLanguageBase      = "es";

	// WEB
	const webAdminBaseDir      = "/admin";
	const webUploadsBaseDir    = "/uploads";
	const webPlaygroundBaseDir = "/playground";
	const webThemeBaseDir      = "/themes/classic";
	const webCommonBaseDir     = "/common";

	// PHP
	const phpClassName               = ".class.php";
	const phpInterfaceName           = ".interface.php";
	const phpControllerClassPrefix   = "c";
	const phpControllerClassDefault  = "cIndex";
	const phpControllerMethodDefault = "__index";
	const phpClassPaths              = "__CLASS_PATHS__";
	const phpSession                 = "__MVC_SESSION__";
	const phpErrorReporting          = E_ALL;

	// DB
	const dbEnabled  = False;
	#const dbType     = "mysql"; No others DB support
	const dbHost     = "localhost";
	const dbUser     = "";
	const dbPass     = "";
	const dbDatabase = "";

	// MAIL
	const mailEnabled = False;
	const mailName    = "";
	const mailUser    = "";
	const mailPass    = "";
	const mailSMTP    = "";

	// TPL
	const tplBaseDir     = "/view";
	const tplCompileDir  = "/cache/tpl_c";
	const tplConfigDir   = "/core/template/configs";
	const tplCacheDir    = "/core/template/cache";
	const tplBaseDefault = "common.base";

	// ****
	// AUTH
	// ****
	// USER
	const U_ALL    = 0; # no login required
	const U_VIEW   = 1;
	const U_CREATE = 2;
	const U_MODIFY = 4;
	const U_ADMIN  = 255; # playground 
	//

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ get_value($const)
	//<[get_value()]
	/**
	 * Funcion que devuelve el valor de la constante.
	 * Util cuando el nombre de la constante se tiene en una variable.
	 *
	 * @access  public
	 * @static
	 * @param   string  $const
	 * @return  int
	 */
	public static function get_value($const)
	{

		return eval("return self::{$const};");

	}
	//>
	//}}}

}
?>
