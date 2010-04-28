<?php
import('core.mail.PHPMailer');

/**
 * Class Mail
 * 
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Mail.class.php 84 2009-07-13 00:40:20Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 * @subpackage  mail
 */
class Mail
{

	// {{{ MEMBERS
	/**
	 * @var     PHPMailer
	 * @access  private
	 */
	private $_mailer = null;

	/**
	 * @var     bool
	 * @access  public
	 */
	public $is_html = false;

	/**
	 * @var     string
	 * @access  public
	 */
	public $line_feed = '\r\n';

	/**
	 * @var     string
	 * @access  public
	 */
	public $subject = '';

	/**
	 * @var     string
	 * @access  public
	 */
	public $body_text = '';

	/**
	 * @var     string
	 * @access  public
	 */
	public $body_html = '';
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
		$this->_mailer = new PHPMailer;

		$this->_mailer->IsSMTP();
		$this->_mailer->Host = Config::mailSMTP;
		$this->_mailer->SMTPAuth = true;
		$this->_mailer->Username = Config::mailUser;
		$this->_mailer->Password = Config::mailPass;
		$this->_mailer->From = Config::mailUser;
		$this->_mailer->FromName = Config::mailName;
	}
	//>
	//}}}
	//{{{ add_address($address, $name="")
	//<[add_address()]
	/**
	 * @access  public
	 * @param   string  $address
	 * @param   string  $name
	 * @return  void
	 */
	public function add_address($address, $name="")
	{
		$this->_mailer->AddAddress($address, $name);
	}
	//>
	//}}}
	//{{{ add_cc($address, $name="")
	//<[add_cc()]
	/**
	 * @access  public
	 * @param   string  $address
	 * @param   string  $name
	 * @return  void
	 */
	public function add_cc($address, $name="")
	{
		$this->_mailer->AddCC($address, $name);
	}
	//>
	//}}}
	//{{{ add_bcc($address, $name="")
	//<[add_bcc()]
	/**
	 * @access  public
	 * @param   string  $address
	 * @param   string  $name
	 * @return  void
	 */
	public function add_bcc($address, $name="")
	{
		$this->_mailer->AddBCC($address, $name);
	}
	//>
	//}}}
	//{{{ add_reply_to($address, $name="")
	//<[add_reply_to()]
	/**
	 * @access  public
	 * @param   string  $address
	 * @param   string  $name
	 * @return  void
	 */
	public function add_reply_to($address, $name="")
	{
		$this->_mailer->AddReplyTo($address, $name);
	}
	//>
	//}}}
	//{{{ send()
	//<[send()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function send()
	{
		$this->_mailer->Subject = $this->subject;

		// Cuerpo del EMail
		if ($this->is_html)
		{
			$this->_mailer->IsHTML(true);
			$this->_mailer->Body = $this->body_html;
			$this->_mailer->AltBody = $this->body_text;
		}
		else
		{
			$this->_mailer->IsHTML(false);
			$this->_mailer->WordWrap = 50;
			$this->_mailer->Body = $this->body_text;
		}

		// Envio de EMail
		return $this->_mailer->Send();
	}
	//>
	//}}}

}
?>
