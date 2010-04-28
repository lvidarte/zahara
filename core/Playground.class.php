<?php
/**
 * Class Playground
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Playground.class.php 28 2009-05-14 14:56:00Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 * @abstract
 */
abstract class Playground extends ControllerBase
{

	//{{{ __construct($filePath=null)
	//<[__construct()]
	/**
	 * @access  public
	 * @param   string   $filePath   La ruta completa al archivo que
	 *                               instancio el la clase.
	 * @return  void
	 */
	public function __construct($filePath)
	{

		// Llamada al constructor de ControllerBase
		parent::__construct($filePath);

		// Seteos TPL
		$this->_set_tpl_basics();

	}
	//>
	//}}}
	//{{{ _get_list($li=true)
	//<[_get_list()]
	/**
	 * @access  protected
	 * @param   bool       $li  Indica si debe retornarse cada link
	 *                          entre <li></li>.
	 * @return  string
	 */
	protected function _get_list($li=true)
	{

		$files = scandir($this->file_path->get_dir_path());
		$_list = '';

		foreach ($files as $file)
		{


			if (preg_match('/'.Config::phpClassName.'$/', $file))
			{
				
				// Nombre de la clase unicamente
				// (sin .class.php)
				$class = preg_replace('/'.Config::phpClassName.'$/', '', $file);


				// $class es Index?
				if ( $class != Config::phpControllerClassDefault )
				{
				
					// Eliminado de prefijo para controladores
					$class = preg_replace('/^'.Config::phpControllerClassPrefix.'/', '', $class);

					$_a  = $this->file_path->get_web_dir();
					$_a .= strtolower($class) . '|' . $class;

					#Router::error404($this->file_path->getFileName(), $file);
					if ($this->file_path->get_file_name() == $file)
					{
						$_link = $this->html->a('actual', $_a);
					}
					else
					{
						$_link = $this->html->a($_a);
					}

					$_list .= ($li) ? $this->html->li($_link) : $_link;
				

				}

			}

		}

		return $_list;

	}
	//>
	//}}}
	//{{{ _set_tpl_basics()
	//<[_set_tpl_basics()]
	/**
	 * Seteos base para cualquier vista
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function _set_tpl_basics()
	{
		$this->tpl->add_css('base');
		$this->tpl->add_js('base');

		$this->tpl->assign('pageTitle', $this->file_path->get_url());
		$this->tpl->assign('playgroundIndexLink', $this->html->a("/playground|Inicio"));
		$this->tpl->assign('playgroundClassLinks', self::_get_list(false));

		$this->tpl->set_template_base('core.playground');
	}
	//>
	//}}}

}
?>
