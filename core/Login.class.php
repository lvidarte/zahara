<?php
import('core.data.DB');
import('core.data.entities.User');

/**
 * Class Login
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Login.class.php 6 2009-04-20 00:20:14Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 */
class Login
{

	//{{{ Members
	/**
	 * @var     User
	 * @access  private
	 */
	private $_user = null;
	//}}}

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct()
	//<[__construct()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function __construct()
	{
		// -------------------------------------------
		// La llamada es desde el formulario de login?
		// -------------------------------------------
		//
		if (isset($_POST['__login__']) && isset($_POST['username']) && isset($_POST['password']))
		{
				
				// Trato de loguear al usuario
				$this->_try_login();
				die();

		}
		// ------------------------------------
		// La llamada es desde un controlador X
		// ------------------------------------
		//
		else
		{
			// Usuario no logueado?
			if ( ! $this->_already_login() )
			{

				// Muestro formulario de login
				Router::redir("/user/login" . Router::get_url());
			
			}

		}
	}
	//>
	//}}}
	//{{{ _try_login()
	//<[_try_login()]
	/**
	 * @access  private
	 * @return  void
	 */
	private function _try_login()
	{

		$username = new DBObject('username', 'varchar:20', $_POST['username']);
		$password = new DBObject('password', 'varchar:32', hash('md5', $_POST['password']));

		$row = DB::query_row("
			SELECT * FROM users
			WHERE username={$username->quote()} AND password={$password->quote()}
			LIMIT 1
		");
		#die(DB::get_last_query());

		// control
		#TPL::show(Utils::dump($row));

		// Vuelvo al form de login si no existe el usuario
		// o carece del permiso basico U_VIEW
		if ($row == null || !($row['level_id'] & Config::U_VIEW))
		{
			Router::redir("/user/login/error{$_POST['__from__']}");
		}

		// Termino si hubo error
		if ( $row instanceof Error )
		{
			$row->death($row->get_message());
		}

		// ------------------
		// Usuario encontrado
		// ------------------
		//
		// Actualizo la hora de ultimo acceso
		$row['last_access'] = date('Y-m-d H:i:s');
		//
		DB::query("
			UPDATE users
			SET last_access='{$row['last_access']}'
			WHERE id_user={$row['id_user']}
			LIMIT 1
		");
		//
		$user = new User($row);
		#TPL::show(Utils::dump($user));


		// ----------------------------
		// Creo la session si no existe
		// ----------------------------
		if ( ! Registry::exists('__SESSION__') )
		{
			Registry::set('__SESSION__', new Session);
		}

		Registry::get('__SESSION__')->set('__USER__', $user);

		// -----------
		// Redireccion
		// -----------
		switch ( $_POST['__from__'] )
		{
		#case '/' :
		case '/user/login' :
			Router::redir('/inbox');
			break;
		default:
			Router::redir($_POST['__from__']);
		}

	}
	//>
	//}}}
	//{{{ _already_login()
	//<[_already_login()]
	/**
	 * @access  private
	 * @return  bool
	 */
	private function _already_login()
	{

		// Para que exista un usuario logueado
		// debe primero existir una session
		if ( ! Registry::exists('__SESSION__') )
		{
			return false;
		}

		// Recien ahora controlo que exista un usuario
		if ( ! Registry::exists('__USER__') )
		{
			return false;
		}

		// Usuario logueado!
		return true;

	}
	//>
	//}}}

}
?>
