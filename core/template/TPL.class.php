<?php
import('core.template.Smarty');

/**
 * TPL class
 *
 * @author    Leonardo Vidarte <lvidarte@gmail.com>
 * @version   $Id: TPL.class.php 137 2010-03-07 21:06:23Z xleo $
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package   view
 */
class TPL extends smarty
{

	/**
	 * Almacena el output de cada llamado
	 * a la funcion parse()
	 *
	 * @var     string
	 * @access  private
	 */
	private $_buffer = null;

	/**
	 * Almacena el path al template base
	 * usado como contenedor de $_buffer
	 *
	 * @var     string
	 * @access  private
	 */
	private $_template_base = null;

	//{{{ __construct()
	//<[__construct()]
	/**
	 * @access  public
	 * @return  void
	 * @todo    Cargar el idioma actual, ademas del de base
	 */
	public function __construct() {

		$this->smarty();	
	
		// Set work dirs
		$this->template_dir = Config::appBaseDir . Config::tplBaseDir;
		$this->compile_dir  = Config::appBaseDir . Config::tplCompileDir;
		$this->config_dir   = Config::appBaseDir . Config::tplConfigDir;
		$this->cache_dir    = Config::appBaseDir . Config::tplCacheDir;

		// No cache
		$this->caching = false;

		// 1 hour cache
		#$this->caching = true;
		#$this->cache_lifetime = 3600;

		// Asignacion de variables comunes a cualquier vista
		$this->_common_assign();

	}
	//>
	//}}}
	//{{{ _common_assign()
	//<[_common_assign()]
	/**
	 * Asignacion de variables comunes a cualquier vista
	 *
	 * @access  private
	 * @return  void
	 */
	private function _common_assign() {

		// Load language
		$this->_language_assign();

		// -----------
		// Common vars
		// -----------
		//
		// From Config:
		$this->assign('__tpl', $this);
		$this->assign('__app_name', Config::appName);
		$this->assign('__app_description', Config::appDescription);
		$this->assign('__app_version', Config::appVersion);
		$this->assign('__app_language_base', Config::appLanguageBase);
		$this->assign('__app_lang', Config::appLanguageBase); # alias
		$this->assign('__web_theme_base_dir', Config::webThemeBaseDir);
		$this->assign('__web_theme', Config::webThemeBaseDir); # alias
		$this->assign('__web_common_base_dir', Config::webCommonBaseDir);
		$this->assign('__web_common', Config::webCommonBaseDir); # alias
		$this->assign('__web_admin_base_dir', Config::webAdminBaseDir);
		$this->assign('__web_admin', Config::webAdminBaseDir); # alias
		$this->assign('__today', date('d-m-Y H:m:s'));
		$this->assign('__today_rfc2822', date('r'));
		$this->assign('__time', time());
		$this->assign('date', new Date);

	}
	//>
	//}}}
	//{{{ _language_assign($languaje=Config::appLanguageBase)
	//<[_language_assign()]
	/**
	 * Lectura de archivo XML de idioma
	 *
	 * @access  private
	 * @param   string    $language  optional
	 * @return  void
	 */
	private function _language_assign($languaje=Config::appLanguageBase) {
		
		$_langDir  = Config::appBaseDir . Config::appConfigBaseDir;
		$_langDir .= "/language/$languaje";
		#die($_langDir);

		if (is_dir($_langDir)) {
		
			// Traigo el listado de archivos
			$files = scandir($_langDir);

			foreach ($files as $file) {


				// Si termina en .xml lo incluyo
				if (preg_match("/\.xml$/", $file) && is_file("$_langDir/$file")) {
		
					$xml = simplexml_load_file("$_langDir/$file");
		
					foreach($xml->text as $opt) {

						// Asigno finalmente la variable al tpl
						$this->assign( (string) $opt['name'], (string) $opt);

					}

					// Asigno todas las listas
					foreach($xml->list as $array) {

						$array_name = (string) $array['name'];
						$array_aux  = array();

						foreach($array as $item) {
							if ( isset($item['name']) ) {
								$array_aux[(string) $item['name']] = (string) $item;
							}
							else {
								$array_aux[] = (string) $item;
							}
						}
						
						if ( count($array_aux) ) {
							$this->assign($array_name, $array_aux);
						}

					}

				}

			}

		}

	} 
	//>
	//}}}
	//{{{ sprintf($values)
	//<[sprintf()]
	/**
	 * Devuelve una cadena resultado de llamar a la funcion
	 * nativa de PHP sprintf.
	 * 
	 * Esta funcion careceria de sentido si hiciera
	 * "solamente" eso...
	 * Lo que la hace especial es el hecho de que controla que
	 * el primer elemento del array sea el nombre de una variable
	 * del objeto tpl y, en ese caso, trae el valor de la misma
	 * y reemplaza (con ese valor) al primer elemento del array
	 * antes de pasarle este a la funcion sprintf.
	 *
	 * De esta manera es posible trabajar con las variables
	 * definidas por el lenguaje (idioma) que estemos usando.
	 *
	 * @todo    mejorar esta ayuda
	 *
	 * @access  public
	 * @param   array    $values  Array conteniendo los parametros
	 *                            a pasar a la funcion nativa sprintf().
	 * @return  void
	 */
	public function sprintf($values) {

		// $values es un array?
		if ( is_array($values) ) {
			
			// $value[0] es una variable del template?
			// Si logro traer su valor, entonces lo es.
			if ( $___ = $this->get($values[0]) ) {

				// Modifico $value[0] antes de pasarlo
				// a la funcion Utils::sprintf(), ya que
				// como primer valor debe ir la cadena
				// con los modificadores %s, %d, etc.
				$values[0] = $___;

			}

			return call_user_func_array('sprintf', $values);
			
		}

		// Mal uso de esta funcion.
		// $values es un string?
		if ( is_string($values) ) {

			if ( $___ = $this->get($values) ) {

				return $___;

			}
			else {

				return $values;
			}

		}

		// Mal uso de esta funcion (2da parte).
		return $values;


	}
	//>
	//}}}
	//{{{ parse($file_tpl, $return=false)
	//<[parse()]
	/**
	 * Almacena en $this->_buffer el resultado de
	 * $this->fetch() que devuelve resultado
	 * de procesar el template con las variables asignadas.
	 *
	 * @todo    mejorar esta ayuda
	 *
	 * @access  public
	 * @param   string   $file_tpl   El nombre del archivo tpl a procesar.
	 *                              Utiliza el mismo formato que la funcion import()
	 *                              Ej: $tpl->parse('admin.users');
	 * @param   boolean  $return    Si se retorna o se almacena el resultado.
	 *
	 * @return  string|void
	 */
	public function parse($file_tpl, $return=false) {
		
		$file_tpl = str_replace(".", Config::appDirSep, $file_tpl);
		$path_tpl = "{$this->template_dir}/{$file_tpl}.tpl";

		if ($return === true) {
			return $this->fetch($path_tpl);
		}
		else {
			$this->_buffer .= $this->fetch($path_tpl);
			#die($this->_buffer);
		}

	}
	//>
	//}}}
	//{{{ add()
	//<[add()]
	/**
	 * Agrega a $this->_buffer el/los valores pasados.
	 * Los valores pueden separarse con comas,
	 * Ej. add(valor1, valor2, ..., valorN)
	 *
	 * @todo    mejorar esta ayuda
	 *
	 * @access  public
	 * @return  void
	 */
	public function add() {

		$args = func_get_args();

		foreach ($args as $arg) {
			$this->_buffer .= $arg;
		}

	}
	//>
	//}}}
	//{{{ clear_buffer()
	//<[clear_buffer()]
	/**
	 * Vacia $this->_buffer
	 *
	 * @access  public
	 * @return  void
	 */
	public function clear_buffer() {

		$this->_buffer = null;

	}
	//>
	//}}}
	//{{{ get_buffer()
	//<[get_buffer()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function get_buffer() {

		return $this->_buffer;

	}
	//>
	//}}}
	//{{{ add_css($file_css, $media='screen')
	//<[add_css()]
	/**
	 * Agrega (siempre que no exista previamente) un CSS
	 * a la lista de hojas de estilo
	 * que utilizara la pagina que se esta generando.
	 *
	 * @todo    mejorar esta ayuda
	 *
	 * @access  public
	 * @param   string   $file_css   El nombre del archivo css a agregar.
	 *                              El nombre puede ponerse sin la
	 *                              extension .css
	 * @param   string   $media     Valor de la propiedad 'media' (Ej. 'screen')
	 * @return  void
	 */
	public function add_css($file_css, $media='screen') {

		// Inicializacion de variables
		$css_list = 'css_list';
		$css = $this->get($css_list);


		// Termina en .css el valor pasado a la funcion?
		if ( ! preg_match('/\.css$/', $file_css) ) {
			$file_css .= '.css';
		}

		$cssInclude = array($file_css, $media);

		// Existe la lista?
		if ($css) {

			// Existe el CSS en dicha lista?
			if ( ! in_array($cssInclude, $css) ) {
				$this->append($css_list, $cssInclude);
			}

		}
		// No existe, la creo
		else {
			$this->append($css_list, $cssInclude);
		}

	}
	//>
	//}}}
	//{{{ add_js($file_js)
	//<[add_js()]
	/**
	 * Agrega (siempre que no exista previamente) un JS
	 * a la lista de scripts
	 * que utilizara la pagina que se esta generando.
	 *
	 * @todo    mejorar esta ayuda
	 *
	 * @access  public
	 * @param   string   $file_js   El nombre del archivo js a agregar.
	 *                             El nombre puede ponerse sin la
	 *                             extension .js
	 * @return  void
	 */
	public function add_js($file_js) {

		// Inicializacion de variables
		$js_list = 'js_list';
		$js = $this->get_template_vars($js_list);

		// Termina en .js el valor pasado a la funcion?
		if ( ! preg_match('/\.js$/', $file_js) ) {
			$file_js .= '.js';
		}

		// Existe $js_list?
		if ( $js ) {
			// Es un array?
			if ( is_array($js) ) {
				if ( ! in_array($file_js, $js) ) {
					$this->append($js_list, $file_js);
				}
			}
			// No es array
			else {
				if ( $js != $file_js ) {
					$this->append($js_list, $file_js);
				}
			}
		}
		// No existe $js_list. Lo creo.
		else {
			$this->assign($js_list, $file_js);
		}

	}
	//>
	//}}}
	//{{{ get($name, $key=null)
	//<[get()]
	/**
	 * NO Es un alias de get_template_vars()
	 *
	 * @access  public
	 * @return  void
	 */
	public function get($name, $key=null) {
		
		$_ = $this->get_template_vars($name);

		return ($key) ? $_[$key] : $_;

	}
	//>
	//}}}
	//{{{ set_template_base($file_tpl)
	//<[set_template_base()]
	/**
	 * @access  public
	 * @param   string  $file_tpl
	 * @return  void
	 */
	public function set_template_base($file_tpl) {

		$this->_template_base = $file_tpl;

	}
	//>
	//}}}
	//{{{ get_template_base()
	//<[get_template_base()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_template_base() {

		return $this->_template_base;

	}
	//>
	//}}}
	//{{{ show($html=null)
	//<[show()]
	/**
	 * Imprime el contenido de $this->_buffer
	 *
	 * OJO: este metodo es static.
	 * Ejemplo de uso: TPL::show($html);
	 *
	 * @todo    mejorar esta ayuda
	 *
	 * @static
	 * @access  public
	 * @return  void
	 */
	public static function show($html=null) {

		// Objeto TPL
		$tpl = View::tpl();

		// Memory usage
		$tpl->assign('__memory_usage', round(memory_get_usage() / 1024 / 1024, 2));
		$tpl->assign('__memory_peak', round(memory_get_peak_usage() / 1024 / 1024, 2));
		$tpl->assign('__memory_limit', ini_get('memory_limit'));

		// Time execution
		$mtime = explode(" ", microtime());
		$tpl->assign(
			'__time_execution',
			round(($mtime[1] + $mtime[0]) - Registry::get('__START_TIME__'), 2)
		);

		// Reemplaza el contenido previo de _buffer si $html !== null
		// Util para casos como TPL::show(Utils::dump($array));
		if ( is_string($html) ) {
			$tpl->clear_buffer();
			$tpl->add($html);
		}

		if ($tpl->get_template_base()) {
			$tpl->assign('__CONTENT__', $tpl->get_buffer());
			$tpl->clear_buffer();
			$tpl->parse($tpl->get_template_base());
		}

		// Response
		header("Status: 200 OK", true, 200);
		header('Content-Type: text/html; charset=utf-8');
		print $tpl->get_buffer();
		exit(0);

	}
	//>
	//}}}

}
?>
