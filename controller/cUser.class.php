<?php
/**
 * Class Controller cUser
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: cIndex.class.php 132 2010-02-15 14:10:49Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     controller
 */
class cUser extends Web
{

	//{{{ login($params)
	//<[login()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function login($params)
	{

		if (isset($_POST['username']) && isset($_POST['password']))
		{
			$login = new Login;
			Registry::set('__LOGIN__', $login);
		}
		else
		{

			// Title
			$this->tpl->assign('page_title', $this->tpl->get('_user_login'));
		
			// *************
			// Message Error
			// *************
			$error = (count($params) && $params[0] == 'error') ? 
				array_shift($params) : false;

			if ($error)
				$this->tpl->assign(
					'error',
					$this->tpl->get('_user_not_found')
				);

			// ************
			// Message From
			// ************
			$from = count($params) ? implode('/', $params) : '';

			if ($from)
				$this->tpl->assign(
					'message',
					$this->tpl->sprintf(array('_user_auth_required', "/$from"))
				);

			// You are here
			$this->tpl->assign('you_are_here', array(
				$this->tpl->get('_user_username'),
				$this->tpl->get('_user_login')
			));

			// Control
			$this->tpl->assign('from', "/$from");
			$this->tpl->parse('core.login');
			$this->tpl->show();

		}

	}
	//>
	//}}}
	//{{{ logout()
	//<[logout()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function logout()
	{
		if ($this->session)
			$this->session->destroy();

		Router::redir("/");
	}
	//>
	//}}}

}
?>
