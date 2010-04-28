<?php
/**
 * Class Captcha
 *
 * Clase que genera imagenes usadas para validacion de formularios.
 * Se trata de una prueba desafio-respuesta utilizada en computacion
 * para determinar cuando el usuario es o no humano.
 * La idea diferenciar a humanos de maquinas,
 * impidiendo a estas ultimas que hagan uso de los formularios.
 *
 * @author      Leonardo Vidarte <lvidarte@gmail.com>
 * @version     $Id: Captcha.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 * @subpackage  captcha
 */
class Captcha
{

	// {{{ Members
	/**
	 * @name $captchaCode
	 * Almacena el nombre que se usara en $_SESSION para guardar el codigo del captcha.
	 *
	 * Esta varible, como las que siguen, deberia ser definida como 'private'...
	 * pero entonces esto no seria php4.
	 *
	 * @var string
	 * @access private
	 * @see setSessionVar()
	 * @see getCode()
	 */
	private $captchaCode = "__CAPTCHA_CODE__";

	/**
	 * Almacena el ancho en pixeles de la imagen.
	 * @private int
	 * @access private
	 */
	private $imgWidth;

	/**
	 * Almacena el alto en pixeles de la imagen.
	 * @private int
	 * @access private
	 */
	private $imgHeight;

	/**
	 * Almacena el tamaño de la fuente.
	 * @private int
	 * @access private
	 */
	private $fontFile	= null;

	/**
	 * Almacena el path al archivo TTF.
	 * @private string 
	 * @access private
	 */
	private $fontSize;

	/**
	 * Almacena la preferencia Case.
	 * Indica si el codigo generado tendra o no mayusculas.
	 * @private int
	 * @access private
	 */
	private $fontCase;

	/**
	 * Almacena el factor de ruido (0->no_ruido, 99->max_ruido).
	 * @private int
	 * @access private
	 */
	private $noiseFactor;

	/**
	 * Almacena el color de la fuente y las lineas de fondo.
	 * @private array
	 * @access private
	 */
	private $fontColor = array("red"=>0, "green"=>0, "blue"=>0);

	/**
	 * Alamacena el color de las lineas (ruido).
	 * @private array
	 * @access private
	 */
	private $noiseColor = array("red"=>0, "green"=>0, "blue"=>0);

	/**
	 * Alamacena el color del fondo.
	 * @private array
	 * @access private
	 */
	private $bgColor = array("red"=>255, "green"=>255, "blue"=>255);
	// }}}

	// -------------
	// BEGIN METHODS
	// -------------

	// {{{ void __construct([[int $width], int $width])
	/**
	 * Recibe dos parametros opcionales para setear el ancho y alto de la imagen.
	 *
	 * @param   int     $width
	 * @param   int     $height
	 * @return  void
	 * @access  public
	 */
	public function __construct($width=150, $height=50)
	{

		$this->setWidth($width);
		$this->setHeight($height);
		$this->setFontColor(0,0,0);
		$this->setFontSize(24);
		$this->setNoiseFactor(50);
		$this->setBgColor(255,255,255);

	}
	// }}}
	// {{{ string getCode()
	/**
	 * Trae el codigo guardado en la sesion.
	 *
	 * @return  string
	 * @access  public
	 */
	public function getCode()
	{

		if ( Registry::exists('__SESSION__') )
		{

			$session = Registry::get('__SESSION__');

			// control
			#TPL::show(Utils::dump($session));

			if ( $session->exists($this->captchaCode) )
			{
				return $session->get($this->captchaCode);
			}

		}
		
		return null;

	}
	// }}}
	// {{{ void setWidth(int $width)
	/**
	 * Setea el ancho de la imagen.
	 *
	 * @param   int     $width
	 * @return  void
	 * @access  public
	 */
	public function setWidth($width)
	{

		if (!$width = $this->checkType($width,'int'))
			$width = 150; // default

		$this->imgWidth = $width;

	}
	// }}}
	// {{{ void setHeight(int $heigth)
	/**
	 * Setea el alto de la imagen.
	 *
	 * @param   int    $height
	 * @return  void
	 * @access  public
	 */
	public function setHeight($height)
	{

		if (!$height = $this->checkType($height,'int'))
			$height = 50; // default

		$this->imgHeight = $height;

	}
	// }}}
	// {{{ boolean sendCaptcha()	
	/**
	 * Crea y envia la imagen al navegador.
	 *
	 * sendCaptcha() controla que el archivo .ttf exista, y en caso contrario no hace nada.
	 * Por lo que es recomendable setear la fuente y aprovechar su return antes de llamarlo.
	 * De esa forma podremos tomar medidas al respecto.
	 *
	 * Aqui hay un ejemplo para aclarar lo anterior:
	 * <code>
	 * <?php
	 * $captcha = new Captcha(200,60);
	 * $captcha->setSessionVar(6);
	 * if ($captcha->setFontFile("font.ttf")) {
	 * 	$captcha->setFontColor("FF3300");
	 * 	$captcha->sendCaptcha()
	 * }
	 * ?>
	 *	</code>
	 *
	 * @return  boolean 
	 * @access  public
	 */
	public function sendCaptcha()
	{

		// Solo sigo si la fuente existe y ha sido generado el codigo	
		if ($this->fontFile && $code = $this->getCode())
		{

			// Creo la imagen
			$captcha = @imagecreate($this->imgWidth, $this->imgHeight) or die();

			// Traigo el color del texto y del fondo
			$fontColor 	= imagecolorallocate($captcha,
														$this->fontColor['red'],
													 	$this->fontColor['green'],
													 	$this->fontColor['blue']);

			$bgColor		= imagecolorallocate($captcha,
														$this->bgColor['red'],
														$this->bgColor['green'],
                                       	$this->bgColor['blue']);

			$noiseColor = imagecolorallocate($captcha,
														$this->noiseColor['red'],
														$this->noiseColor['green'],
                                       	$this->noiseColor['blue']);

			// Relleno el fondo
			imagefill($captcha, 0, 0, $bgColor);

			// Dibujo algunas lineas aleatoriamente en caso que noiseFactor > 0
			if ($this->noiseFactor > 0)
			{

				// En base a noiseFactor y la superficie de la imagen
				// calculo la cantidad de lineas a dibujar
				$noise = ($this->imgWidth * $this->imgHeight) / ((100 - $this->noiseFactor) * 10);
				
				// Finalmente... el dibujo de lineas al azar
				for($i=0; $i<$noise; $i++)
				{
					imageline($captcha, mt_rand(0,$this->imgWidth), mt_rand(0,$this->imgHeight),
								 mt_rand(0,$this->imgWidth), mt_rand(0,$this->imgHeight), $noiseColor);
				}
			}

			// Calculo los margenes para que el texto quede siempre centrado
			$left = $this->imgWidth / (strlen($code) + 2);
			$top = (($this->imgHeight - $this->fontSize) / 2) + $this->fontSize;
			$l = "";

			// Escribo los caracteres variando su tamaño entre (+/-)20%
			// y rotandolos entre (+/-)15°
			for ($i=0; $i<strlen($code); $i++)
			{
				$l += $left;
				imagettftext($captcha, mt_rand($this->fontSize*1.2, $this->fontSize*0.8),
								 mt_rand(-15,15), $l, $top, $fontColor, $this->fontFile, $code[$i]);
			}

			// Envio headers
			header("Content-type: image/jpeg");

			// Envio la imagen
			imagejpeg($captcha);

			// Borro el temp
			imagedestroy($captcha);

			return true;
		}

		return false;

	}
	// }}}
	// {{{ void setSessionVar(int $lentgh)
	/**
	 * Crea el codigo y lo setea en $_SESSION[$this->captchaCode]
	 * 
	 * @param   int     $length
	 * @return  void
	 * @access  public
	 */
	public function setSessionVar($length)
	{

		if (!$length = $this->checkType($length,'int'))
		{
			$length = 6; // default
		}

		// ----------------------------
		// Creo la session si no existe
		// ----------------------------
		$session = Registry::exists('__SESSION__') ? Registry::get('__SESSION__') : new Session;

		$session->set($this->captchaCode, $this->genCode($length));

		Registry::set('__SESSION__', $session);

	}
	// }}}
	// {{{ void unsetSessionVar()
	/**
	 * unset de $_SESSION[$this->captchaCode]
	 *
	 * @return  void
	 * @access  public
	 */
	public function unsetSessionVar()
	{

		if ( Registry::exists('__SESSION__') )
		{

			$session = Registry::get('__SESSION__');

			// control
			#TPL::show(Utils::dump($session));

			if ( $session->exists($this->captchaCode) )
			{
				return $session->unregister($this->captchaCode);
			}

		}

	}
	// }}}
	// {{{ string genCode(int $length)
	/**
	 * Genera un codigo alfanumerico aleatorio de $length caracteres.
	 * Recibe como parametro la cantidad de caracteres que debera tener el codigo.
	 * No deberia ser necesario usar este metodo, ya que setSessionVar hace todo el trabajo.
	 *
	 * @param   int     $length
	 * @return  string
	 * @access  public
	 */
	public function genCode($length)
	{

		if (!$length = $this->checkType($length,'int'))
			$length = 6; // default

		$code='';

		for ($i=0; $i<$length; $i++)
		{
			$code .= $this->getChar();
		}

		return $code;

	}
	// }}}
	// {{{ string getChar()
	/**
	 * Metodo privado que devuelve caracteres al azar
	 *
	 * @return  string
	 * @access  private
	 */
	public function getChar()
	{

		mt_srand((double)microtime()*1000000);

		$random = mt_rand(1, $this->fontCase);

		switch ($random)
		{

			// [0-9]
			case 1: $char = mt_rand(48, 57); break;

			// [a-z]
			case 2: $char = mt_rand(97, 122); break;

			// [A-Z]
			case 3: $char = mt_rand(65, 90); break;

		}

		return chr($char);
	}
	// }}}
	// {{{ boolean setFontFile(string $fontFile)
	/**
	 * Setea el archivo .ttf a usar y devuelve true si el archivo existe o false en caso contrario.
	 * <code>
	 * <?php
	 * if (setFontFile("font.ttf")) {
	 *    sendCaptcha();
	 * }
	 * ?>
	 * </code>
	 * El archivo .ttf debe estar en el mismo directorio que el script que envia
	 * la imagen al navegador. (Esto esta para arreglar/mejorar).
	 * La clase solo funciona con un archivo .ttf, si este no existe no es posible
	 * generar un captcha.
	 *
	 * @param   string   $fontFile
	 * @return  boolean
	 * @access  public
	 */
	public function setFontFile($fontFile)
	{

		// El archivo debe estar junto con la clase
		// Esto esta para revisar / mejorar / corregir?
		//putenv('GDFONTPATH=' . realpath('.'));
		
		if (file_exists($fontFile))
		{
			$this->fontFile = $fontFile;
			return true;
		}
		else
		{
			$this->fontFile = null;
			return false;
		}

	}
	// }}}
	// {{{ void setFontSize(int $size)
	/**
	 * Setea el size de la fuente. 
	 *
	 * @param   int     $size
	 * @return  void
	 * @access  public
	 */
	public function setFontSize($size)
	{
		if (!$this->fontSize = $this->checkType($size,'int'))
			$this->fontSize = 24; // default
	}
	// }}}
	// {{{ void setFontColor(int $red, int $green, int $blue)
	/**
	 * Setea el color de los caracteres.
	 * Recibe tres valores de tipo entero (0-255) para especificar el valor de los canales
	 * red, green y blue respectivamente.
	 *
	 * @see setFontColorWeb()
	 *
	 * @param   int     $red
	 * @param   int     $green
	 * @param   int     $blue
	 * @return  void
	 * @access  public
	 */
	public function setFontColor($red, $green, $blue)
	{

		if (!$this->fontColor['red'] = $this->checkType($red,'int'))
		{
			$this->fontColor['red'] = 0; // default
			$this->noiseColor['red'] = 0; // default
		}
		else $this->noiseColor['red'] = $red;
			
		if (!$this->fontColor['green'] = $this->checkType($green,'int'))
		{
			$this->fontColor['green'] = 0; // default
			$this->noiseColor['green'] = 0; // default
		}
		else $this->noiseColor['green'] = $green;
			
		if (!$this->fontColor['blue'] = $this->checkType($blue,'int'))
		{
			$this->fontColor['blue'] = 0; // default
			$this->noiseColor['blue'] = 0; // default
		}
		else $this->noiseColor['blue'] = $blue;

	}
	// }}}
	// {{{ void setFontColorWeb(string $hex)
	/**
	 * Setea el color de los caracteres.
	 * Recibe un string de 6 caracteres hexadecimales del tipo RRGGBB.
	 *
	 * Por una cuestion de comodidad setFontColor y setFontColorWeb()
	 * setean tambien los colores del ruido ($this->noiseColor).
	 * De manera que siempre utilice los metodos setNoiseColor() y setNoiseColorWeb()
	 * 'despues', a menos que quiera escribir una linea de codigo inutil. :)
	 *
	 * @see setFontColor()
	 *
	 * @param   string   $hex
	 * @return  void
	 * @access  public
	 */
	public function setFontColorWeb($hex)
	{

		if (strlen($hex) != 6 || !$hex = $this->checkType($hex,'hex'))
			$hex = '000000'; // default
		
		$red   = substr($hex,0,2);
		$green = substr($hex,2,2);
		$blue  = substr($hex,4,2);
		$this->setFontColor(hexdec($red), hexdec($green), hexdec($blue));
		// Por una cuestion de comodidad setFontColor() y setFontColorWeb()
		// setean tambien los colores del ruido ($this->noiseColor).
		// Por ello usar siempre setNoiseColor o setNoiseColorWeb despues de
		// 
		$this->setNoiseColor(hexdec($red), hexdec($green), hexdec($blue));

	}
	// }}}
	// {{{ void setFontCase([boolean $caseSensitive])
	/**
	 * Define la preferencia Case Sensitive con respecto al codigo generado. 
	 * Recibe un booleano que indica si el codigo tendra mayusculas y minusculas
	 * o solo minusculas (para captchas menos complejos)
	 *
	 * @param   boolean  $caseSentitive
	 * @return  void
	 * @access  public
	 */
	public function setFontCase($caseSensitive=true)
	{

		$this->fontCase = ($caseSensitive) ? 3 : 2;

	}
	// }}}
	// {{{ void setBgColor(int $red, int $green, int $blue)
	/**
	 * Setea el color del fondo.
	 * Recibe tres valores de tipo entero (0-255) para especificar el valor de los canales
	 * red, green y blue respectivamente.
	 *
	 * @see setBgColorWeb()
	 *
	 * @param   int    $red
	 * @param   int    $green
	 * @param   int    $blue
	 * @return  void
	 * @access  public
	 */
	public function setBgColor($red, $green, $blue)
	{

		if (!$this->bgColor['red'] = $this->checkType($red,'int'))
			$this->bgColor['red'] = 255; // default
			
		if (!$this->bgColor['green'] = $this->checkType($green,'int'))
			$this->bgColor['green'] = 255; // default
			
		if (!$this->bgColor['blue'] = $this->checkType($blue,'int'))
			$this->bgColor['blue'] = 255; // default

	}
	// }}}
	// {{{ void setBgColorWeb(string $hex)
	/**
	 * Setea el color del fondo.
	 * Recibe un string de 6 caracteres hexadecimales del tipo RRGGBB
	 *
	 * @see setFontColorWeb()
	 *
	 * @param   string  $hex
	 * @return  void
	 * @access  public
	 */
	public function setBgColorWeb($hex)
	{

		if (strlen($hex) != 6 || !$hex = $this->checkType($hex,'hex'))
			$hex = 'FFFFFF'; // default

		$red   = substr($hex,0,2);
		$green = substr($hex,2,2);
		$blue  = substr($hex,4,2);
		$this->setBgColor(hexdec($red), hexdec($green), hexdec($blue));

	}
	// }}}
	// {{{ void setNoiseColor(int $red, int $green, int $blue)
	/**
	 * Setea el color de las lineas de fondo (ruido).
	 * Recibe tres valores de tipo entero (0-255) para especificar el valor de los canales
	 * red, green y blue respectivamente.
	 * 
	 * @see setNoiseColorWeb()
	 *
	 * @param   int    $red
	 * @param   int    $green
	 * @param   int    $blue
	 * @return  void
	 * @access  public
	 */
	public function setNoiseColor($red, $green, $blue)
	{

		if (!$this->noiseColor['red'] = $this->checkType($red,'int'))
			$this->noiseColor['red'] = 0; // default
			
		if (!$this->noiseColor['green'] = $this->checkType($green,'int'))
			$this->noiseColor['green'] = 0; // default
			
		if (!$this->noiseColor['blue'] = $this->checkType($blue,'int'))
			$this->noiseColor['blue'] = 0; // default

	}
	// }}}
	// {{{ void setNoiseColorWeb(string $hex)
	/**
	 * Setea el color de las las lineas de fondo (ruido).
	 * Recibe un string de 6 caracteres hexadecimales del tipo RRGGBB
	 *
	 * @see setNoiseColor()
	 *
	 * @param   string  $hex
	 * @return  void
	 * @access  public
	 */
	public function setNoiseColorWeb($hex)
	{

		if (strlen($hex) != 6 || !$hex = $this->checkType($hex,'hex'))
			$hex = '000000'; // default
		
		$red   = substr($hex,0,2);
		$green = substr($hex,2,2);
		$blue  = substr($hex,4,2);
		$this->setNoiseColor(hexdec($red), hexdec($green), hexdec($blue));

	}
	// }}}
	// {{{ void setNoiseFactor(int $noise)
	/**
	 * Define el factor de ruido para el fondo de la imagen.
	 * Recibe un entero entre 0 y 99, donde 0 es sin ruido y 99 es el maximo posible de ruido.
	 *
	 * @param   int     $noise
	 * @return  void
	 * @access  public
	 */
	public function setNoiseFactor($noise)
	{
		
		if (!($noise>=0 && $noise<=99) || !$this->noiseFactor = $this->checkType($noise,'int'))
			$this->noiseFactor = 50; // default

	}
	// }}}
	// {{{ int|srting|false checkType(mixed $val, srting $type)
	/**
	 * Chequea que los valores sean del tipo que corresponde.
	 * Recibe como primer parametro la variable a chequear y como segundo
	 * un string con el tipo esperado.
	 *
	 * @param   mixed    $val
	 * @param   string   $type
	 * @return  int|string|false
	 * @access  private
	 */
	public function checkType($val, $type)
	{
		switch ($type)
		{
			case 'int' : return (is_int($val)) ? (int) $val : false; break;
			case 'str' : return (is_string($val)) ? $val : false; break;
			case 'hex' : return (preg_match("/^[0-9A-F]+$/i",$val)) ? $val : false; break;
		}
	}
	// }}}

}
?>
