<?php
/**
 * Class HTML
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: HTML.class.php 1 2009-03-11 11:29:08Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 */
class HTML {

	//{{{ table($values, $caption, $style, $forceRowKeys, $forceColKeys, $sortRows, $sortCols, $nullValue, $return)
	//<[table()]
	/**
	 * Alias de HTML::vtable()
	 *
	 * @access  public
	 *
	 * @param   array   $values         Array con los datos de la tabla.
	 * @param   string  $caption        El titulo de la tabla.
	 * @param   string  $style          Estilos por default para th y td
	 *                                  respectivamente.
	 *                                  Ej. "center right"
	 * @param   bool    $forceRowKeys   Se muestran los indices de fila?
	 * @param   bool    $forceColKeys   Se muestran los indices de columna?
	 * @param   bool    $sortRows       Se ordenan las filas?
	 * @param   bool    $sortCols       Se ordenan las columnas?
	 * @param   bool    $nullValue      Valor para campos null.
	 * @param   bool    $return         Se retorna el resultado?
	 *
	 * @return  mixed
	 */
	public function table(
		$values,
		$caption      = null,
		$style        = null,
		$forceRowKeys = true,
		$forceColKeys = true,
		$sortRows     = false,
		$sortCols     = false,
		$nullValue    = '&nbsp;',
		$return       = true
	) {

		if ($return) {

			return $this->vtable(
				$values, $caption, $style, 
				$forceRowKeys, $forceColKeys,
				$sortRows, $sortCols, 
				$nullValue, $return
			);

		}
		else {

			$this->vtable(
				$values, $caption, $style, 
				$forceRowKeys, $forceColKeys, 
				$sortRows, $sortCols, 
				$nullValue, $return
			);

		}

	}
	//>
	//}}}
	//{{{ _table($values, $caption, $style, $forceRowKeys, $forceColKeys, $sortRows, $sortCols, $return)
	//<[_table()]
	/**
	 * Alias de HTML::vtable() que por default devuelve la tabla HTML.
	 *
	 * @access  public
	 *
	 * @param   array   $values         Array con los datos de la tabla.
	 * @param   string  $caption        El titulo de la tabla.
	 * @param   string  $style          Estilos por default para th y td
	 *                                  respectivamente.
	 *                                  Ej. "center right"
	 * @param   bool    $forceRowKeys   Se muestran los indices de fila?
	 * @param   bool    $forceColKeys   Se muestran los indices de columna?
	 * @param   bool    $sortRows       Se ordenan las filas?
	 * @param   bool    $sortCols       Se ordenan las columnas?
	 * @param   bool    $nullValue      Valor para campos null.
	 * @param   bool    $return         Se retorna el resultado?
	 *
	 * @return  mixed
	 */
	public function _table(
		$values,
		$caption      = null,
		$style        = null,
		$forceRowKeys = true,
		$forceColKeys = true,
		$sortRows     = false,
		$sortCols     = false,
		$nullValue    = '&nbsp;',
		$return       = false
	) {

		return $this->vtable($values, $caption, $style, 
			$forceRowKeys, $forceColKeys, $sortRows, $sortCols, 
			$nullValue, true);

	}
	//>
	//}}}
	//{{{ vtable($values, $caption, $style, $forceRowKeys, $forceColKeys, $sortRows, $sortCols, $nullValue, $return)
	//<[vtable()]
	/**
	 * @access  public
	 *
	 * @param   array   $values         Array con los datos de la tabla.
	 * @param   string  $caption        El titulo de la tabla.
	 * @param   string  $style          Estilos por default para th y td
	 *                                  respectivamente.
	 *                                  Ej. "center right"
	 * @param   bool    $forceRowKeys   Se muestran los indices de fila?
	 * @param   bool    $forceColKeys   Se muestran los indices de columna?
	 * @param   bool    $sortRows       Se ordenan las filas?
	 * @param   bool    $sortCols       Se ordenan las columnas?
	 * @param   bool    $nullValue      Valor para campos null.
	 * @param   bool    $return         Se retorna el resultado?
	 *
	 * @return  mixed
	 */
	public function vtable(
		$values,
		$caption      = null,
		$style        = null,
		$forceRowKeys = true,
		$forceColKeys = true,
		$sortRows     = false,
		$sortCols     = false,
		$nullValue    = '&nbsp;',
		$return       = true
	) {


		// -----------------------
		// Iniciacion de variables
		// -----------------------
		// 
		// Filas de la tabla: <tr></tr>
		$rows    = array();
		//
		// Indices de fila
		$rowKeys = array();
		// 
		// Indices de columna
		$colKeys = array();
		//
		// Estilos CSS:
		// $styles[0] => clase para <table>
		// $styles[1] => clase para <th>
		// $styles[2] => clase para <td>
		$styles  = ($style) ? explode(' ', $style, 3) : null;
		//
		// Tabla final
		$table   = null;


		// --------------------------------
		// Obtencion de indices de columnas
		// --------------------------------
		foreach ($values as $value) {
			if ( is_array($value) ) {
				foreach ($value as $key => $val) {
					if ( ! in_array($key, $colKeys) )
						$colKeys[] = $key;
				}
			}
		}


		// ------------
		// Ordenamiento
		// ------------
		//
		// Se ordenan las filas?
		if ( $sortRows ) ksort($values);
		//
		// Se ordenan las columnas?
		if ( $sortCols ) sort($colKeys);
		//
		// Reset del array
		reset($values);


		// -------------------
		// Recorrido del array
		// -------------------
		for ($i = 0; $i < count($values); $i++) {

			// Obtengo el elemento, que puede
			// ser un array o un tipo basico
			// como string, int, float.
			$value = each($values);
			
			// Guardo indice de fila
			$rowKeys[] = $value['key'];


			// ---------
			// Es array?
			// ---------
			if (is_array($value['value'])) {

				// Recorro el total de columnas
				for ($j = 0; $j < count($colKeys); $j++) {

					// Existe el indice $colKeys[$j]?
					// De paso, creo una referencia.
					$__ =& $value['value'][$colKeys[$j]];

					// Valor por default para campos null
					if ( $__ === null ) $__ = $nullValue;

					// Hay estilo por default para TD?
					@$rows[$i] .= ( isset($styles[2]) ) ?
						$this->td($styles[2], $__) :
						$this->td($__);

				}

			}

			// -----------
			// No es array
			// -----------
			else {

				// Hay estilo por default para TD?
				@$rows[$i] .= ( isset($styles[2]) ) ?
					$this->td($styles[2], $value['value']) :
					$this->td($value['value']);

			}

		}
		#Utils::printArray($rows);


		// -----------------------------------
		// Se muestran los indices de columna?
		// -----------------------------------
		if ( $forceColKeys && count($colKeys) ) {

			foreach ($colKeys as $_i) {

				// Hay estilo por default para TH?
				@$_aux .= ( isset($styles[1]) ) ?
					$this->th($styles[1], $_i) :
					$this->th('center', $_i);

			}

			// Agrego los indices al principio
			array_unshift($rows, $_aux);

		}
		#Utils::printArray($rows);


		// -----------------------------------
		// Se muestran los indices de columna?
		// -----------------------------------
		if ( $forceRowKeys ) {

			if ( $forceColKeys && count($colKeys) ) {
				array_unshift($rowKeys, null);
			}

			for ($i = 0; $i < count($rows); $i++) {

				if ( $rowKeys[$i] === null ) {
					$rows[$i] = $this->th('noth', $rowKeys[$i]) . $rows[$i];
				}
				else {
					$rows[$i] = ( isset($styles[1]) ) ?
						$this->th($styles[1], $rowKeys[$i]) . $rows[$i] :
						$this->th($rowKeys[$i]) . $rows[$i];
				}

			}

		}
		#Utils::printArray($rows);


		// ------------------
		// Titulo de la tabla
		// ------------------
		if ($caption) {
			$table .= $this->caption($caption);
		}

		// -------------------
		// Construyo las filas
		// -------------------
		foreach ($rows as $file) {
			$table .= $this->tr($file);
		}

		// ----------------
		// Termino la tabla
		// ----------------
		$styl3 = ( isset($styles[0]) ) ? ' class="'.$styles[0].'"' : '';
		$table = "<table$styl3>\n$table</table>\n";

		// ---------------
		// return OR print
		// ---------------
		if ($return) return $table;
		else print $table;

	}
	//>
	//}}}
	//{{{ htable($values, $caption, $style, $forceRowKeys, $forceColKeys, $return)
	//<[htable()]
	/**
	 * @access  public
	 * @param   array   $values         Array con los datos de la tabla.
	 * @param   string  $caption        El titulo de la tabla.
	 * @param   string  $style          Estilos por default para th y td
	 *                                  respectivamente.
	 *                                  Ej. "center right"
	 * @param   bool    $forceRowKeys   Se muestran los indices de fila?
	 * @param   bool    $forceColKeys   Se muestran los indices de columna?
	 * @param   bool    $nullValue      Valor para campos null.
	 * @param   bool    $return         Se retorna el resultado?
	 * @return  mixed
	 */
	public function htable(
		$values,
		$caption      = null,
		$style        = null,
		$forceRowKeys = true,
		$forceColKeys = true,
		$nullValue    = '&nbsp;',
		$return       = true
	) {

		// -----------------------
		// Iniciacion de variables
		// -----------------------
		// 
		$files = array();
		$keys  = array();
		$table = null;
		$rows  = 1;
		$cols  = count($values);
		$style = ($style) ? explode(' ', $style, 2) : null;

		// Obtengo la cantidad max de filas
		foreach ($values as $value) {
			if (is_array($value) && count($value) > $rows)
				$rows = count($value);
		}

		// Reset del array
		reset($values);
		#die(print_r($values, true));

		// Recorro las columnas
		for ($i = 0; $i < $cols; $i++) {

			$value = each($values);
			
			if (is_array($value['value'])) {

				if ($forceColKeys) {
					if ( isset($files[0]) ) {
						// Hay estilo por default para TH?
						$files[0] .= ( isset($style[0]) ) ? 
							$this->th($style[0], $value['key']) :
							$this->th($value['key']);
					}
					else {
						// Hay estilo por default para TH?
						$files[0]  = ( isset($style[0]) ) ? 
							$this->th($style[0], $value['key']) :
							$this->th($value['key']);
					}
				}

				for ($j = 1; $j <= $rows; $j++) {

					$col = @each($value['value']);
					
					if ( ! $col ) {
						$col['value'] = '&nbsp;';
					}

					if ( isset($files[$j]) ) {
						// Hay estilo por default para TD?
						$files[$j] .= ( isset($style[1]) ) ?
							$this->td($style[1], $col['value']) :
							$this->td($col['value']);	
					}
					else {
						// Hay estilo por default para TD?
						$files[$j]  = ( isset($style[1]) ) ?
							$this->td($style[1], $col['value']) :
							$this->td($col['value']);	
					}

					// Agrego el nombre de la columna a $keys[]
					if (isset($col['key']) && 
						is_string($col['key']) &&
						!in_array($col['key'], $keys)) {

						$keys[] = $col['key'];

					}

				}

			}
			else {

				if ( isset($files[0])) {
					// Hay estilo por default para TH?
					$files[0] .= ( isset($style[0]) ) ?
						$this->th($style[0], $value['key']) :
						$this->th($value['key']);
				}
				else {
					// Hay estilo por default para TH?
					$files[0]  = ( isset($style[0]) ) ?
						$this->th($style[0], $value['key']) :
						$this->th($value['key']);
				}

				if ( isset($files[1]) ) {
					// Hay estilo por default para TD?
					$files[1] .= ( isset($style[1]) ) ?
						$this->td($style[1], $value['value']) :
						$this->td($value['value']);	
				}
				else {
					// Hay estilo por default para TD?
					$files[1]  = ( isset($style[1]) ) ?
						$this->td($style[1], $value['value']) :	
						$this->td($value['value']);	
				}

			}

		}
		#die(print_r($files, true));

		// Construyo los titulos de las filas
		if ( $forceRowKeys && count($keys) ) {

			if (isset($files[0])) $files[0] = $this->td('noborder', '&nbsp') . $files[0];

			for ($i = 0; $i < count($keys); $i++) {

				// Hay estilo por default para TH?
				if ( isset($style[0]) ) {
					$files[$i+1] = $this->th($style[0], $keys[$i]) . $files[$i+1];
				}
				else {
					$files[$i+1] = $this->th('left', $keys[$i]) . $files[$i+1];
				}

			}

		}
		#die(print_r($files, true));

		// Construyo las filas
		foreach ($files as $file) {
			$table .= $this->tr($file);
		}

		// Titulo de la tabla
		if ($caption) {
			$table = $this->caption($caption) . $table;
		}

		// Termino la tabla
		$table = "<table>\n$table</table>\n";

		// return OR print
		if ($return) return $table;
		else print $table;

	}
	//>
	//}}}
	//{{{ _getAlign(&$str)
	//<[_getAlign()]
	/**
	 * @access  private
	 * @param   string   $str  El texto a alinear.
	 * @return  string
	 */
	private function _getAlign(&$str) {

		// Salgo si se trata de un array
		// Esto es para etiquetas que se construyen desde un array: Ej. <select>
		if (is_array($str)) return;

		// Guardo el original
		$_str = (is_object($str)) ? $str->__toString() : $str;

		// Modifico la referencia
		$str  = trim($_str);

		if ( preg_match('/^ /', $_str) ) {

			// Center
			if ( preg_match('/ $/', $_str) ) {
				return 'center';
			}
			// Right
			else {
				return 'right';
			}

		}
		else {

			// Left 
			if ( preg_match('/ $/', $_str) ) {
				return 'left';
			}
			// None
			else {
				return null;
			}

		}
	}
	//>
	//}}}
	//{{{ _clean(&$params)
	//<[_clean()]
	/**
	 * Esta funcion busca y elimina clases de alineacion
	 * tales como right, left y center.
	 *
	 * @access  private
	 * @param   array    $params  El array a limpiar.
	 * @return  string
	 */
	private function _clean(&$params) {

		// Las clases a eliminar
		$_aligns = array('left', 'right', 'center');

		// La cantidad de elementos
		$length = count($params);

		// Tiene que ser > 1 para que haya estilos
		if ( $length > 1 ) {

			// Recorro el array menos el ultimo elemento
			for ($i = 0; $i < ($length-1); $i++) {

				$styles = explode(' ', $params[$i]);

				#Utils::printArray($styles);

				$finded = false;

				for ($j = 0; $j < count($styles); $j++) {
					if ( in_array($styles[$j], $_aligns) ) {
						unset($styles[$j]);
						$finded = true;
					}
				
				}

				#Utils::printArray($styles);

				if ($finded && $j == 0) {
					unset($params[$i]);
				}
				else {
					$params[$i] = implode(' ', $styles);
				}


			}

		}


	}
	//>
	//}}}
	//{{{ __call($tag, $params)
	//<[__call]
	/**
	 * @access  public
	 * @param   string   $tag     Nombre del tag
	 * @param   array    $params  Array de parametros
	 * @return  mixed
	 */
	public function __call($tag, $params) {

		// New Line
		$_nl = "\n";

		// Tags que pueden no tener parametros
		$_noparams = array('br', 'hr');

		// Salida, si el array esta vacio
		if (count($params) == 0 && !in_array($tag, $_noparams)) return;

		// Obtengo la ultima posicion del array
		$length = count($params) - 1;

		// Analizo el tag para saber
		// si debo retornar el resultado.
		// El nombre de la funcion comienza con _?
		if ( preg_match('/^_/', $tag) ) {
			$return = false;
			$tag    = preg_replace('/^_/','',$tag);
		}
		else {
			$return = true;
		}

		// Analizo la alineacion del valor
		if ( $align = $this->_getAlign($params[$length]) ) {

			// Borro todos los estilos de alineacion
			// que encuentre.
			$this->_clean($params);

			// Cargo el nuevo
			array_unshift($params, $align);

			// Calculo el nuevo largo del array
			$length = count($params) - 1;
			
			#Utils::dump($params);

		}

		// Inicio tag apertura
		$html  =  '<' . $tag;

		// Analisis para detectar tags especiales.
		// Esto deberia ser un metodo aparte?
		switch ($tag) {

			// --------
			// <select>
			// --------
			case 'select' :

				#TPL::show(Utils::dump($params));

				$s = $params[$length];
				$params[$length] = '';

				$name = isset($s['name']) ? $s['name'] : '';
				$id = isset($s['id']) ? $s['id'] : $name;
				$html .= " name=\"$name\" id=\"$id\"";

				foreach ($s['options'] as $key => $value) {
					$selected = (isset($s['selected']) && $key == $s['selected']) ?
						' selected="selected"' : '';
					$params[$length] .= "$_nl<option name=\"$key\"$selected>$value</option>";
				}
				$params[$length] .= $_nl;

				break;

			// -----
			// <img>
			// -----
			case 'img' :

				$_img = explode('|', $params[$length], 2);

				$html .= ' src="' . $_img[0] . '"';

				if (isset($_img[1])) {
					$html .= ' alt="' . $_img[1] . '"';
				}

				break;

			// ---
			// <a>
			// ---
			case 'a' :
			
				$_a = explode('|', $params[$length], 3);

				#TPL::show(Utils::dump($params[$length]));
						
				if (isset($_a[2])) {

					switch ($_a[2]) {

						case 'javascript' :
						case 'jscript'    :
						case 'js'         :
							$html .= " href=\"javascript:;\" onclick=\"{$_a[0]}\"";
							break;

						default: 
							$html .= " href=\"{$_a[0]}\" title=\"{$_a[2]}\"";
							$params[$length] = $_a[1];
							break;

					}

				}
				elseif (isset($_a[1])) {

					switch ($_a[1]) {

						case 'anchor' :
							$html .= " name=\"{$_a[0]}\"";
							$params[$length] = '';
							break;

						default: 
							$html .= " href=\"{$_a[0]}\" title=\"{$_a[1]}\"";
							$params[$length] = $_a[1];
							break;

					}
					
				}
				else {
					$html .= " href=\"{$_a[0]}\" title=\"\"";
				}

				$_nl = '';
				break;


			// ------------
			// <script>
			// ------------
			case 'script' :
				
					#TPL::show(Utils::dump($params));
					$html .= ' type="text/javascript"';
					$params[$length] = $_nl.'//<![CDATA['.$_nl.$params[$length].$_nl.'//]]>'.$_nl;

					break;

		}

		// Hay clases?
		if ( count($params) > 1 ) {
			
			$html .=  ' class="';
			
			// Agrego las clases
			$space = '';
			for ($i = 0; $i < $length; $i++) {

				if ( $params[$i] ) {
					$html .= $space . $params[$i];
					$space = ' ';
				}

			}

			$html .= '"';
		}

		// ------------------------
		// Control cierre etiquetas
		// ------------------------
		switch ($tag) {

			case 'br'  :
			case 'hr'  :
			case 'img' :	
				$html .= " />$_nl";
				break;

			case 'span'   :
			case 'strong' :
			case 'a'      :
				$html .= ">{$params[$length]}</$tag>";
				break;

			default:
				$html .= ">{$params[$length]}</$tag>$_nl";

		}

		// return OR print
		if ($return) return $html;
		else print $html;


	}
	//>
	//}}}

}
?>
