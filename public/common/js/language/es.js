/**
 * JS language ES
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: es.js 123 2009-12-02 17:53:42Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     common
 * @subpackage  js
 * @subpackage  language
 */

// {{{ String.prototype.conv()
//<[String.prototype.conv()]
String.prototype.conv = function() {
	str = this;
	str = str.replace(/{a}/g,'\341');
	str = str.replace(/{e}/g,'\351');
	str = str.replace(/{i}/g,'\355');
	str = str.replace(/{o}/g,'\363');
	str = str.replace(/{u}/g,'\372');
	str = str.replace(/{n}/g,'\361');
	str = str.replace(/{N}/g,'\321');
	return str;
}
//>
// }}}

//var _alert = 'Atenci{o}n\n'.conv();
var _lang = new Array();

// {{{ errors

// User
_lang['error_user_username'] = 'No ha ingresado el nombre de usuario.';
_lang['error_user_pass'] = 'No ha ingresado la contrase{n}a o la confirmaci{o}n de la misma.'.conv();
_lang['error_user_pass_dont_match'] = 'Las contrase{n}as no coinciden.'.conv();
_lang['error_user_name'] = 'No ha ingresado el nombre del usuario.';
_lang['error_user_lastname'] = 'No ha ingresado el apellido del usuario.';

// Sector
_lang['error_sector_description'] = 'No ha ingresado el nombre del sector.';
_lang['error_sector_exists'] = 'El sector ya existe.';

// Place
_lang['error_place_description'] = 'No ha ingresado el nombre del lugar.';
_lang['error_place_exists'] = 'El lugar ya existe.';

// Document
_lang['error_document_title'] = 'No ha ingresado el t{i}tulo del documento.'.conv();
_lang['error_document_exists'] = 'El documento ya existe.';

// Product
_lang['error_product_title'] = 'No ha ingresado el nombre del producto.';
_lang['error_product_exists'] = 'El producto ya existe.';

// Theme
_lang['error_theme_description'] = 'No ha ingresado el nombre del tema.';
_lang['error_theme_exists'] = 'El tema ya existe.';

// Discovery
_lang['error_discovery_product_extra'] = 'Todos los productos debe tener su correspondiente Nro de Lote.';
_lang['error_discovery_text_discovery'] = 'No ha ingresado el texto del Hallazgo.';
_lang['error_discovery_document'] = 'Debe seleccionar un documento o ingresar el detalle del mismo.';

// Task
_lang['error_discovery_task_description'] = 'No ha ingresado el texto de la Planificaci{o}n.'.conv();

// Action
_lang['error_action_text_planification'] = 'No ha ingresado el texto de la Acci{o}n.'.conv();

// Ticket
_lang['error_ticket_users'] = 'No ha seleccionado el destinatario del Mensaje.';
_lang['error_ticket_text_summary'] = 'No ha ingresado el asunto del Mensaje.';
_lang['error_ticket_text_message'] = 'No ha ingresado el texto del Mensaje.';

// EMail
_lang['error_mail_users'] = 'No ha seleccionado usuarios a ser notificados.';
// }}}
// {{{ messages confirm
_lang['confirm'] = 'Por favor confirme la acci{o}n.'.conv();
_lang['confirm_discovery_delete'] = 'Se eliminar{a} el hallazgo %s.\n\nAtenci{o}n!\nesto borrar{a} todas las disposiciones, acciones,\nseguimientos y tickets relacionados.'.conv();
_lang['confirm_discovery_task_delete'] = 'Se eliminar{a} la disposici{o}n %s.\n\nAtenci{o}n!\nesto borrar{a} todas las evidencias y\ntickets relacionados.'.conv();
_lang['confirm_action_delete'] = 'Se eliminar{a} la acci{o}n %s.\n\nAtenci{o}n!\nesto borrar{a} todos los seguimientos y\ntickets relacionados.'.conv();
_lang['confirm_ticket_delete'] = 'Se eliminar{a} el mensaje %s.\n\nAtenci{o}n!\nesto borrar{a} todos los mensajes por debajo\ndel nivel actual.'.conv();
_lang['confirm_file_delete'] = 'Se eliminar{a} el archivo %s.\n\nAtenci{o}n!\nesta acci{o}n es irreversible.'.conv();
_lang['confirm_place_delete'] = 'Se eliminar{a} el lugar %s.\n\nAtenci{o}n!\nesta acci{o}n es irreversible.'.conv();
_lang['confirm_document_delete'] = 'Se eliminar{a} el documento %s.\n\nAtenci{o}n!\nesta acci{o}n es irreversible.'.conv();
_lang['confirm_product_delete'] = 'Se eliminar{a} el producto %s.\n\nAtenci{o}n!\nesta acci{o}n es irreversible.'.conv();
_lang['confirm_theme_delete'] = 'Se eliminar{a} el tema %s.\n\nAtenci{o}n!\nesta acci{o}n es irreversible.'.conv();
_lang['confirm_sector_delete'] = 'Se eliminar{a} el sector %s.\n\nAtenci{o}n!\nesta acci{o}n es irreversible.'.conv();
_lang['confirm_user_delete'] = 'Se eliminar{a} el usuario %s.\n\nAtenci{o}n!\nesta acci{o}n es irreversible.'.conv();
// }}}

