<?php
#import('core.HTML');

/**
 * Class Tools
 *
 * @author      Leonardo Vidarte <lvidarte@gmail.com>
 * @version     $Id: Utils.class.php 80 2009-07-06 15:54:55Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  misc
 * @abstract
 */
abstract class Utils
{

	//{{{ print_code($file, $label=null, $fileName=false, $lineNumbers=true, $showLabel=true, $isPHP=true, $return=true)
	//<[print_code()]
	/**
	 * Imprime la porcion de codigo marcada entre //< y //>
	 * La etiqueta [label] es opcional y sirve para referenciar
	 * un bloque especifico dentro del archivo.
	 *
	 * //<[label]
	 *
	 * ...
	 *
	 * //>
	 *
	 * @access  public
	 * @static
	 * @param   string   $file         El archivo que tiene el codigo a mostrar.
	 *                                 Puede contener valores como:
	 *                                 - model.core.Utils
	 *                                 - /var/www/fireblog/model/core/Utils.class.php
	 *                                 - //config/languages/es/common.xml
	 *                                 (donde // es la raiz del sitio web)
	 * @param   string   $label        El nombre del bloque a mostrar.
	 * @param   bool     $fileName     Se imprime el nombre del archivo?
	 * @param   bool     $lineNumbers  Se imprimen los numeros de linea?
	 * @param   bool     $showLabel    Se imprime label?
	 * @param   bool     $isPHP        El archivo tiene codigo PHP?
	 * @param   bool     $return       Se devuelve el resultado?
	 * @return  void
	 */
	public static function print_code(
		$file,
		$label        = null,
		$fileName     = false,
		$lineNumbers  = true,
		$showLabel    = true,
		$isPHP        = true,
		$return       = true
	) {

		// Objeto HTML
		$html = new HTML;

		// --------------------
		// Analisis de $file
		// --------------------

		// Coincide con la forma 'model.core.Utils'?
		if ( ! preg_match('/\\'.Config::appDirSep.'/', $file) )
		{

			// Guardo el nombre original
			$_file = $file;
			if ($label) $_file .= '::' . $label;

			$file = str_replace('.', Config::appDirSep, $file); // deja-vu...
			$file = Config::appBaseDir . "/$file";

			// Traigo el archivo
			if ( file_exists($file . Config::phpClassName) )
				$lines = file($file . Config::phpClassName);
			// O FIN
			else
				return null;

		}
		else
		{

			// Coincide con la forma '//config/languages/es/common.xml'?
			if ( preg_match('/^\/\//', $file) )
			{

				// Saco el primer '/' y construyo el link
				$_file = preg_replace('/^\//', '', $file);
				$_file = $html->a($_file);
				$file  = preg_replace('/^\/\//', Config::appBaseDir, $file);

			}
			else
			{
				$_file = $file;
			}

			// Traigo el archivo
			if ( file_exists($file) )
			{
				$lines = file($file);
				#TPL::show(Utils::dump($lines));
			}
			// O FIN
			else return null;

		}


		// ---------------------------
		// Inicializacion de variables
		// ---------------------------
		$_label   = null;
		$num      = null;
		$comment  = null;
		$code     = null;
		$_out     = ($fileName) ? $html->h4('filePath', $html->code($_file)) : '';
		$print    = false;

		// Expresiones regulares para etiquetas de apertura y cierre
		$regexOpenTag         = '/^\s*\/\/\</';          # //<
		$regexCloseTag        = '/^\s*\/\/\>/';          # //>
		$regexOthersOpenTags  = '/\s*(\/\*|<!--)\n$/';   # /* o <!--
		$regexOthersCloseTags = '/^\s*(\*\/|-->)\s*\n/'; # *\ o -->

		// Inicializacion de variables del for()
		$i        = 0;
		$iMax     = count($lines);


		//------------------------------------
		// Recorrido de las lineas del archivo 
		//------------------------------------
		for ($i; $i < $iMax; $i++)
		{

			//-------------------
			// Comienzo de bloque
			//-------------------
			// Mientras //<
			//-------------------
			while ( $i < $iMax && preg_match($regexOpenTag, $lines[$i]) )
			{

				// Obtengo el comentario
				$commentLine = preg_replace($regexOpenTag, '', $lines[$i]);
				$commentLine = trim($commentLine);

				// Obtengo el nombre del bloque (label)
				$matches = array();
				if ( preg_match('/^\[([^\]]+)]/', $commentLine, $matches) ) {
					#Utils::print_array($matches);
					$_label = $matches[1];
				}

				// Se pidio un bloque especifico?
				if ( $label )
				{

					// El bloque pedido coincide
					// con el encontrado?
					if ($_label == $label)
					{
						if ($commentLine != "[$_label]")
						{
							$commentLine  = htmlspecialchars($commentLine);
							$commentLine  = preg_replace('/ /','&nbsp;',$commentLine);
							$comment     .= $commentLine . '<br />';
						}
						$print = true; //xD
					}
					// No es el bloque.
					// Muevo el puntero hasta la linea
					// que contiene el tag de cierre //>
					else
					{
						$_label = null;
						do
						{
							$i++;
						} while ($i < $iMax && !preg_match($regexCloseTag, $lines[$i]));
					}

				}
				// No se pidio un bloque especifico.
				// Se muestran todos.
				else
				{

					if ($commentLine != "[$_label]")
					{
						$commentLine  = preg_replace('/ /','&nbsp;',$commentLine);
						$comment     .= $commentLine . '<br />';
					}
					$print = true; //xD

				}

				// Muevo el puntero a la siguiente linea,
				// la cual puede ser una de las siguientes:
				// a. La primer linea de codigo,
				//    luego del bloque comentarios.
				// b. La linea siguiente al tag de cierre //>
				//    Este caso se da cuando:
				//    se pide un bloque especifico y
				//    el bloque encontrado no es el pedido.
				$i++;

			}

			//----------------------
			// Fin de bloque
			//----------------------
			// Volcado de resultados
			//----------------------

			// Se encontro una etiqueta de cierre //> ?
			if ( $i < $iMax && preg_match($regexCloseTag, $lines[$i]) )
			{

				// Nombre del bloque 
				if ( $showLabel && $_label )
				{
					$_out .= $html->div('fileLabel', $html->code($_label));
				}

				// Comentario
				if ($comment && $comment != '<br />') { 
					$_out .= $html->div('fileComment', $html->code($comment));
				}

				// Numeros de linea
				if ($lineNumbers && $num != '') { 
					$_out .= $html->div('fileNumbers', $html->code($num));
				}

				// Hay codigo?
				if ( trim($code) ) { 

					// Formato codigo PHP
					if ($isPHP)
					{
						// Resaltado de sintaxis
						$code    = highlight_string("<?php\n$code?>", true);

						// Saco la etiqueta inicial de php
						$code    = str_replace('&lt;?php<br />','',$code);

						// Saco la etiqueta final de php
						$code    = str_replace('<span style="color: #0000BB">?&gt;</span>','',$code);
					}

					// Formato de codigo no PHP
					else
					{
						// Saco comentarios al principio
						$code    = preg_replace($regexOthersCloseTags,'',$code);

						// Saco comentarios al final
						$code    = preg_replace($regexOthersOpenTags,'',$code);

						// Convierto a entidades
						$code    = htmlspecialchars($code);

						// Reemplazo tabuladores
						$code    = preg_replace('/\t/','&nbsp;&nbsp;&nbsp;',$code);

						// Reemplazo espacios
						$code    = preg_replace('/ /','&nbsp;',$code);

						// Cambio fin de linea por <br />
						$code    = preg_replace('/\n/','<br />',$code);

						// Finalmente la cadena completa
						$code    = $html->code($code);
					}

					$_out      .= $html->div('fileCode', $code);
				}

				// Reset
				$code     = null;
				$num      = null;
				$_label   = null;
				$comment  = null;
				$print    = false;
			}


			// -------------------------
			// Colector de lineas codigo
			// -------------------------
			if ($print && $i < $iMax)
			{

				if ($lineNumbers)
				{
					$num .= ($i + 1) . "<br />";
				}

				$code .= $lines[$i];
					
			}

		}

		$_out = $html->div('fileCodeBlock', $_out);

		// return OR print
		if ($return) return $_out;
		else print $_out;

	}
	//>
	//}}}
	//{{{ _print_code($file, $label=null, $fileName=false, $lineNumbers=true, $showLabel=true, $isPHP=true)
	//<[_print_code()]
	/**
	 * Imprime la porcion de codigo marcada entre //< y //>
	 * La etiqueta [label] es opcional y sirve para referenciar
	 * un bloque especifico dentro del archivo.
	 *
	 * @see     print_code()
	 *
	 * @static
	 * @param   string   $file         El archivo que tiene el codigo a mostrar.
	 *                                 Puede contener valores como:
	 *                                 - model.core.Utils
	 *                                 - /var/www/fireblog/model/core/Utils.class.php
	 *                                 - //config/languages/es/common.xml
	 *                                 (donde // es la raiz del sitio web)
	 * @param   string   $label        El nombre del bloque a mostrar.
	 * @param   bool     $fileName     Se imprime el nombre del archivo?
	 * @param   bool     $lineNumbers  Se imprimen los numeros de linea?
	 * @param   bool     $showLabel    Se imprime label?
	 * @param   bool     $isPHP        El archivo tiene codigo PHP?
	 * @return  void
	 */
	public static function _print_code(
		$file,
		$label        = null,
		$fileName     = false,
		$lineNumbers  = true,
		$showLabel    = true,
		$isPHP        = true
	) {
		
		self::print_code($file, $label, $fileName, $lineNumbers, $isPHP, false);

	}
	//>
	//}}}
	//{{{ print_file($file, $label=null, $filename=true, $showLabel=true, $return=true)
	//<[print_file()]
	/**
	 * Es una forma rapida de usar print_code para archivos que no son PHP.
	 * Finalmente se termina llamando a la funcion print_code,
	 * pasandole la opcion $isPHP=false, lo que desactiva el resaltado de
	 * la sintaxis.
	 * Ademas de esto, por default, print_file no imprime los numeros
	 * de lineas... fixme: no tengo claro todavia si esto es bueno o malo. 
	 *
	 * @see     print_code()
	 *
	 * @access  public
	 * @static
	 * @param   string   $file         El archivo que tiene el codigo a mostrar.
	 *                                 Puede contener valores como:
	 *                                 - model.core.Utils
	 *                                 - /var/www/fireblog/model/core/Utils.class.php
	 *                                 - //config/languages/es/common.xml
	 *                                 (donde // es la raiz del sitio web)
	 * @param   string   $label        El nombre del bloque a mostrar.
	 * @param   bool     $filename     Se imprime el nombre del archivo?
	 * @param   bool     $lineNumbers  Se imprimen los numeros de linea?
	 * @param   bool     $showLabel    Se imprime label?
	 * @param   bool     $return       Se devuelve el resultado?
	 * @return  void
	 */
	public static function print_file(
		$file,
		$label        = null,
		$filename     = true,
		$lineNumbers  = false,
		$showLabel    = true,
		$return       = true
	) {

		if ($return)
		{
			return self::print_code($file, $label, $filename, $lineNumbers, $showLabel, false);
		}
		else
		{
			self::print_code($file, $label, $filename, $lineNumbers, $showLabel, false, false);
		}


	}
	//>
	//}}}
	//{{{ print_array($array, $wrap=false, $return=true)
	//<[print_array()]
	/**
	 * Imprime el dump de un array en un formato "un poco" mas ameno
	 * que la funcion print_r().
	 *
	 * Quiero hacer enfasis en "un poco".
	 *
	 * En realidad la cosa pasa por el CSS.
	 *
	 * @access  public
	 * @static
	 * @param   array    $array    The array to be printed.
	 * @param   bool     $wrap     TRUE to wrap long lines.
	 * @param   bool     $return   TRUE to make this function return the code.
	 * @return  void
	 */
	public static function print_array($array, $wrap=false, $return=true)
	{

		$html = new HTML;

		if ($wrap)
		{
			
			$_out = $html->pre('wrap',
				$html->code( print_r($array, true) )
			);

		}
		else
		{

			$_out = $html->pre( $html->code( print_r($array, true) ) );

		}

      // return OR print
		if ($return) return $_out;
		else
		{
			$tpl = View::tpl();
			$tpl->add($_out);
		}

	}
	//>
	//}}}
	//{{{ _print_array($array, $wrap=false)
	//<[_print_array()]
	/**
	 * Imprime el dump de un array en un formato "un poco" mas ameno
	 * que la funcion print_r().
	 *
	 * @see     print_array()
	 *
	 * @access  public
	 * @static
	 * @param   array    $array    The array to be printed.
	 * @param   bool     $wrap     TRUE to wrap long lines.
	 * @return  void
	 */
	public static function _print_array($array, $wrap=false)
	{

		self::print_array($array, $wrap, false);

	}
	//>
	//}}}
	//{{{ print_r($array, $wrap=false)
	//<[print_r()]
	/**
	 * Alias de print_array()
	 *
	 * @see     print_array()
	 *
	 * @access  public
	 * @static
	 * @param   array    $array    The array to be printed.
	 * @param   bool     $wrap     TRUE to wrap long lines.
	 * @return  string
	 */
	public static function print_r($array, $wrap=false)
	{

		return self::print_array($array, $wrap);

	}
	//>
	//}}}
	//{{{ dump($object)
	//<[dump()]
	/**
	 * Alias de Utils::print_array()
	 *
	 * @access  public
	 * @static
	 * @param   array    $object 
	 * @param   bool     $wrap     TRUE to wrap long lines.
	 * @param   bool     $return   TRUE to make this function return the code.
	 * @return  void
	 */
	public static function dump($object, $wrap=false, $return=true)
	{

		/*
		ob_start();
		var_dump($object);
		$output = ob_get_contents();
		ob_end_clean();
		 */

		if ($return)
		{
			return self::print_array($object, $wrap);
		}
		else
		{
			self::print_array($object, $wrap, false);
		}

	}
	//>
	//}}}
	//{{{ sprintf( &$params=null )
	//<[sprintf()]
	/**
	 * Llama a la funcion sprintf() pasandole los
	 * parametros contenidos en el array
	 * y retorna su resultado.
	 *
	 * @access  public
	 * @static
	 * @param   array   $params   Array en el cual, el primer elemento
	 *                            es tomado como el string que se le pasa
	 *                            a la funcion sprintf y el resto como
	 *                            cada uno de los parametros siguientes.
	 *                            
	 * @return  mixed             Si $params es un array devuelve
	 *                            el string resultado de
	 *                            la llamada a la funcion sprintf.
	 *                            Si se pasa algo distinto a un array
	 *                            se devuelve tal cual.
	 */
	public static function sprintf( &$params=null )
	{

		if ( is_array($params) )
		{

			return call_user_func_array('sprintf', $params);

		}
		else
		{

			return $params;

		}

	}
	//>
	//}}}
	//{{{ remove_accents($string)
	//<[remove_accents()]
	/**
	 * Ref: http://htmlhelp.com/reference/charset/
	 *
	 *
	 * @access  public
	 * @static
	 * @param   array   $string
	 * @return  string
	 */
	public static function remove_accents($string) {

		// ÀÁÂÃÄÅ
		$tofind  = chr(192) . chr(193) . chr(194) . chr(195) . chr(196) . chr(197);
		$replac  = "AAAAAA";
		// ÈÉÊË
		$tofind .= chr(200) . chr(201) . chr(202) . chr(203);
		$replac .= "EEEE";
		// ÌÍÎÏ
		$tofind .= chr(204) . chr(205) . chr(206) . chr(207);
		$replac .= "IIII";
		// ÒÓÔÕÖØ
		$tofind .= chr(210) . chr(211) . chr(212) . chr(213) . chr(214) . chr(216);
		$replac .= "OOOOOO";
		// ÙÚÛÜ
		$tofind .= chr(217) . chr(218) . chr(219) . chr(220);
		$replac .= "UUUU";

		// àáâãäå
		$tofind .= chr(224) . chr(225) . chr(226) . chr(227) . chr(228) . chr(229);
		$replac .= "aaaaaa";
		// èéêë
		$tofind .= chr(232) . chr(233) . chr(234) . chr(235);
		$replac .= "eeee";
		// ìíîï
		$tofind .= chr(236) . chr(237) . chr(238) . chr(239);
		$replac .= "iiii";
		// òóôõöø 
		$tofind .= chr(242) . chr(243) . chr(244) . chr(245) . chr(246) . chr(248);
		$replac .= "oooooo";
		// ùúûü
		$tofind .= chr(249) . chr(250) . chr(251) . chr(252);
		$replac .= "uuuu";

		// ÇçÿÑñ
		$tofind .= chr(199) . chr(231) . chr(255) . chr(209) .chr(241);
		$replac .= "CcyNn";

		//°
		$tofind .= chr(176) . chr(186);
		$replac .= "oo";
		

		return strtr($string, $tofind, $replac);

	}
	//>
	//}}}

}
?>
