<?php
#import('core.Error');

/**
 * Class Admin
 *
 * Contiene metodos basicos para el manejo del admin
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Admin.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 * @abstract
 */
abstract class Admin extends ControllerBase
{

	/**
	 * URLs de las secciones del admin
	 *
	 * @access  protected
	 * @var     array
	 */
	protected $_data = array();

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct($file_path=null)
	//<[__construct()]
	/**
	 * @access  public
	 * @param   string  $file_path  La ruta completa al archivo
	 * @return  void
	 */
	public function __construct($file_path)
	{

		// Llamada al constructor de ControllerBase
		parent::__construct($file_path);

		// Inicializacion del objeto
		$this->__init();

		// Seteos TPL
		$this->_set_tpl_basics();

		// Muestro el menu
		$this->load_menu();

		// Seteo algunas variables del TPL
		$this->set_tpl();

	}
	//>
	//}}}
	//{{{ __init()
	//<[__init()]
	/**
	 * @access  protected
	 * @return  void
	 */
	protected function __init()
	{
		
		// Init del array
		$this->_data = array(
			//{{{ node
			'node' => array(

				'insert' => array(
					'name' => $this->tpl->get('_form_node_insert'),
					'url'  => Config::admin . '/node/insert',
					'menu' => true
				),

				'update' => array(
					'name' => $this->tpl->get('_form_node_update'),
					'url'  => Config::admin . '/node/update',
					'menu' => false
				),

				'delete' => array(
					'name' => $this->tpl->get('_form_node_delete'),
					'url'  => Config::admin . '/node/delete',
					'menu' => false
				),

				'ls' => array(
					'name' => $this->tpl->get('_form_node_ls'),
					'url'  => Config::admin . '/node/ls',
					'menu' => true
				),

				'relation' => array(
					'name' => $this->tpl->get('_form_node_relation'),
					'url'  => Config::admin . '/node/relation',
					'menu' => true
				)

			),
			//}}}
			//{{{ manufacturer
			'manufacturer' => array(

				'insert' => array(
					'name' => $this->tpl->get("_form_manufacturer_insert"),
					'url'  => Config::admin . "/manufacturer/insert",
					'menu' => true
				),

				'update' => array(
					'name' => $this->tpl->get("_form_manufacturer_update"),
					'url'  => Config::admin . "/manufacturer/update",
					'menu' => false
				),

				'delete' => array(
					'name' => $this->tpl->get("_form_manufacturer_delete"),
					'url'  => Config::admin . "/manufacturer/delete",
					'menu' => false
				),

				'ls' => array(
					'name' => $this->tpl->get("_form_manufacturer_ls"),
					'url'  => Config::admin . "/manufacturer/ls",
					'menu' => true
				)

			),
			//}}}
			//{{{ product
			'product' => array(

				'insert' => array(
					'name' => $this->tpl->get('_form_product_insert'),
					'url'  => Config::admin . '/product/insert',
					'menu' => true
				),

				'update' => array(
					'name' => $this->tpl->get('_form_product_update'),
					'url'  => Config::admin . '/product/update',
					'menu' => false
				),

				'delete' => array(
					'name' => $this->tpl->get('_form_product_delete'),
					'url'  => Config::admin . '/product/delete',
					'menu' => false
				),

				'ls' => array(
					'name' => $this->tpl->get('_form_product_ls'),
					'url'  => Config::admin . '/product/ls',
					'menu' => false
				),

				'full_list' => array(
					'name' => 'Full-List',
					'url'  => Config::admin . '/product/full_list',
					'menu' => true
				)
			),
			//}}}
			//{{{ transaction
			'transaction' => array(

				'delete' => array(
					'name' => $this->tpl->get('_form_transaction_delete'),
					'url'  => Config::admin . '/transaction/delete',
					'menu' => false
				),

				'update' => array(
					'name' => $this->tpl->get('_form_transaction_update'),
					'url'  => Config::admin . '/transaction/update',
					'menu' => false
				),

				'ls' => array(
					'name' => $this->tpl->get('_form_transaction_ls'),
					'url'  => Config::admin . '/transaction/ls',
					'menu' => true
				)

			),
			//}}}
			//{{{ options
			'options' => array(

				'update' => array(
					'name' => $this->tpl->get('_form_options_update'),
					'url'  => Config::admin . '/options/update',
					'menu' => true
				)

			),
			//}}}
			//{{{ calc
			'calc' => array(

				'products' => array(
					'name' => $this->tpl->get('_form_calc_products'),
					'url'  => Config::admin . '/calc/products',
					'menu' => true
				),

				'users' => array(
					'name' => $this->tpl->get('_form_calc_users'),
					'url'  => Config::admin . '/calc/users',
					'menu' => true
				)

			)
			//}}}
		);

	}
	//>
	//}}}
	//{{{ get_menu()
	//<[get_menu()]
	/**
	 * @param   bool       $li  Indica si debe retornarse cada link
	 *                          entre <li></li>.
	 * @return  string
	 */
	protected function get_menu($li=true)
	{

		$links = '';

		foreach ($this->_data as $class => $methods)
		{

			foreach ($methods as $method => $data)
			{

				if ( ! $data['menu'] ) continue;

				$link = $this->html->a($data['url']."|".$data['name']);

				$links .= ($li) ? $this->html->li($link) : $link;				

			}

		}

		// control
		#TPL::show(Utils::dump($list));

		return $links;

	}
	//>
	//}}}
	//{{{ load_user()
	//<[load_user()]
	/**
	 * Polimorfismo
	 *
	 * @access  public
	 * @return  void
	 */
	public function load_user()
	{

		parent::load_user();
		$this->tpl->parse('user');

	}
	//>
	//}}}
	//{{{ load_menu()
	//<[load_menu()]
	/**
	 * @access  protected
	 * @return  void
	 */
	protected function load_menu()
	{

		$this->tpl->assign('menu', $this->get_menu(false));
		$this->tpl->parse('admin_menu');

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

		$this->tpl->assign('obj_name', $this->_name);
		$this->tpl->assign('url_base', Config::admin . '/' . $this->_name);
		$this->tpl->assign('url_insert', @$this->_data[$this->_name]['insert']['url']);
		$this->tpl->assign('url_update', @$this->_data[$this->_name]['update']['url']);
		$this->tpl->assign('url_delete', @$this->_data[$this->_name]['delete']['url']);

	}
	//>
	//}}}
	//{{{ __call()
	//<[__call()]
	/**
	 * @method
	 * @access  public
	 * @param   string   $name    nombre del metodo
	 * @param   array    $params  parametros pasados al metodo
	 * @return  void
	 */
	public function __call($name, $params)
	{

		// Para poder usar $this->title('update')
		// y traer el titulo del form update
		if ( $name == 'title' ) $name = 'name';


		// El nombre de la funcion es un numero?
		if ( is_numeric($name) )
		{
			// urls tipo /admin/node/56
			$this->__index( (int) $name );
		}
		elseif ( strlen($name) == 39 )
		{
			// urls tipo /admin/transaction/072310-460D0D
			$this->__index( (string) $name );
		}
		// Un solo parametro?
		elseif ( count($params) == 1 )
		{
			// $this->delete(7) se convierte en /admin/elemento/delete/7 
			if ( is_numeric($params[0]) || strlen($params[0]) == 39 )
			{
				return Config::admin . '/' . $this->_name . '/' . $params[0];
			}
			// $this->url('ls') 
			elseif ( isset($this->_data[$this->_name][$params[0]][$name]) )
			{
				return $this->_data[$this->_name][$params[0]][$name];
			}
		}
		// Dos parametros?
		elseif ( count($params) == 2 )
		{
			// para agregar un query al url: $this->url('insert', '?from=x')
			// el resultado: _name/insert/?from=x
			if ( $name == 'url' && isset($this->_data[$this->_name][$params[0]]['url']) )
			{
				return $this->_data[$this->_name][$params[0]]['url'] . '/' . $params[1];
			}
			// especifico: $this->url('node', 'insert')
			elseif ( isset($this->_data[$params[0]][$params[1]][$name]) )
			{
				return $this->_data[$params[0]][$params[1]][$name];
			}
		}
		else
		{
			$error = new Error;
			$error->death();
		}

	}
	//>
	//}}}

}
?>
