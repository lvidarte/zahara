<?php
import('core.mail.Mail');

/**
 * Class Notify
 * 
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Notify.class.php 102 2009-11-23 15:30:30Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 * @subpackage  mail
 */
class Notify extends Mail
{

	// {{{ MEMBERS
	/**
	 * @var     TPL
	 * @access  public
	 */
	public $tpl = null;
	// }}}

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
		parent::__construct();

		// TPL
		$this->tpl = View::get_tpl();
	}
	//>
	//}}}
	//{{{ add_users($to, $object, $users)
	//<[add_users()]
	/**
	 * @access  public
	 * @param   string  $to      all|rest|none|users
	 * @param   Object  $object  Discovery|Task|Action|Ticket
	 * @param   array   $users   users list to send email 
	 * @return  bool
	 */
	public function add_users($to, $object, $users)
	{
		if ($to == 'none' || ($to == 'users' && count($users) == 0))
			return false;

		$_users = array(); // 'address' => 'username'

        // ******************
        // SPECIFIC USER LIST
        // ******************
        if ($to == 'users')
        {
            foreach ($users as $_user)
            {
                $user = new User;
                $user->set_from_db($_user['id_user']);
                if ($user->email != '')
                    $_users[$user->email] = $user->username;
            }
            #TPL::show(Utils::dump($_users));
        }
        // *************
        // USERS RELATED
        // *************
        else
        {
		    $actual_user = Registry::get('__USER__');

            // **********
            // AUTHORIZED
            // **********
            // En caso de existir solo se envia a
            // los usuarios autorizados
            $authorized = $object->item_users->get_by_type('authorized');
            //
            foreach ($authorized as $user)
                if ($user->id_user != $actual_user->id_user &&
                        $user->email != '')
                    $_users[$user->email] = $user->username;

            // *******
            // CREATOR
            // *******
            if (count($authorized) == 0 && !$object instanceOf Discovery)
            {
                $user = $object->get_user_creator();
                if ($user->id_user != $actual_user->id_user && 
                        $user->email != '')
                    $_users[$user->email] = $user->username;
            }

            // *********
            // IN_CHARGE
            // *********
            if (count($authorized) == 0)
                foreach ($object->item_users->get_by_type('in_charge') as $user)
                    if ($user->id_user != $actual_user->id_user &&
                            $user->email != '')
                        $_users[$user->email] = $user->username;

            // ***********
            // ACTUAL USER
            // ***********
            if ($to == 'all' && $actual_user->email != '')
                $_users[$actual_user->email] = $actual_user->username;
        }

        // TPL
        #TPL::show(Utils::dump($_users));
        $this->tpl->assign('users', $_users);

		// ADD USERS
		foreach ($_users as $address => $username)
			$this->add_address($address, $username);

		return (count($_users) ? true : false);
	}
	//>
	//}}}
	//{{{ send_mail($to, $object, $action='create', $users=array())
	//<[send()]
	/**
	 * @access  public
	 * @param   string  $to      all|rest|none|users
	 * @param   Object  $object  Discovery|Task|Action|Ticket
	 * @param   string  $action  create|modify
	 * @param   array   $users   users list to send mail 
	 * @return  bool
	 */
	public function send_mail($to, $object, $action='create', $users=array())
	{
		if (!Config::mailEnabled)
			return false;

		if (!$this->add_users($to, $object, $users))
			return false;

		// *********
		// Discovery
		// *********
		if ($object instanceOf Discovery)
		{
			$this->subject = $this->tpl->sprintf(array(
				$this->tpl->get('_discovery_mail_subject'),
				$this->tpl->get("_mail_{$action}"),
				$object->id_discovery
			));
			$tpl = 'discovery/mail_body';
		}

		// ****
		// Task
		// ****
		elseif ($object instanceOf Task)
		{
			$this->subject = $this->tpl->sprintf(array(
				$this->tpl->get('_discovery_task_mail_subject'),
				$this->tpl->get("_mail_{$action}"),
				$object->id_task
			));
			$tpl = 'discovery/task/mail_body';
		}

		// ******
		// Action
		// ******
		elseif ($object instanceOf Action)
		{
			$this->subject = $this->tpl->sprintf(array(
				$this->tpl->get('_action_mail_subject'),
				$this->tpl->get("_mail_{$action}"),
				$object->id_action
			));
			$tpl = 'action/mail_body';
		}

		// ******
		// Ticket
		// ******
		elseif ($object instanceOf Ticket)
		{
			$this->subject = $this->tpl->sprintf(array(
				$this->tpl->get('_ticket_mail_subject'),
				$object->get_user_creator()->username,
				$object->text_summary
			));
			$tpl = 'ticket/mail_body';
		}

		$this->subject = Utils::remove_accents(
			html_entity_decode($this->subject)
		);
		
		$this->tpl->assign('subject', $this->subject);
		$this->tpl->assign('o', $object);

		$this->body_text = Utils::remove_accents(
			html_entity_decode($this->tpl->parse($tpl, true))
		);

        /* Debug
        TPL::show(sprintf("<pre>%s\n%s</pre>",
            $this->subject, $this->body_text));
        //*/

		return $this->send();
	}
	//>
	//}}}

}
?>
