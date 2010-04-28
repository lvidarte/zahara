<?php
/**
 * Web Interface Entry Point
 *
 * Punto de entrada de la aplicacion
 * MVC (Model View Controller)
 *
 * @author   Leonardo Vidarte <lvidarte@gmail.com>
 * @version  $Id: index.php 1 2009-03-11 11:29:08Z xleo $
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

/**
 * Obtencion directorio raiz
 */
#print dirname(__FILE__);
preg_match('/^(\/.+)+(\/\w+)$/', dirname(__FILE__), $matches);
#print_r($matches);
define('APP_BASE_DIR', $matches[1]);
define('APP_PUBLIC_BASE_DIR', $matches[2]);

/**
 * Carga de parametros de configuracion
 */
require_once('../config/Config.class.php');

/**
 * Carga de funciones. Ej: import()
 */
require_once('../core/init.php');

/*
 * Carga de clases principales
 */
import('core.*');

/**
 * Delegacion de tarea al controlador 
 * MVC running...
 */
Router::delegate();
?>
