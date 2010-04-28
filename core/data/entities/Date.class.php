<?php
import('core.data.entities.iDBEntity');

/**
 * Class Date
 * 
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: Date.class.php 91 2009-08-31 02:23:09Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  entities
 */
class Date implements iDBEntity
{

	/**
	 * @var     int
	 * @access  private
	 */
	private $_timestamp;

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct($date=null)
	//<[__construct()]
	/**
	 * @access  public
	 * @parama  string|int  $date
	 * @return  void
	 */
	public function __construct($date=null)
	{

		if (is_string($date))
		{
			$this->set_timestamp(strtotime($date));
		}
		elseif (is_int($date))
		{
			$this->set_timestamp($date);
		}
		else
		{
			$this->set_timestamp(time());
		}

	}
	//>
	//}}}
	//{{{ __get($key)
	//<[__get()]
	/**
	 * @access  public
	 * @param   string  $key
	 * @return  mixed
	 */
	public function __get($key)
	{

		switch ($key)
		{
			case 'datetime': return $this->get('Y-m-d H:i:s'); break;
			case 'dmy': return $this->get('d-m-Y'); break;
			case 'ymd': return $this->get('Y-m-d'); break;
			case 'hms': return $this->get('H:i:s'); break;
			case 'hm': return $this->get('H:i'); break;

			case 'yy': return $this->get('y'); break;
			case 'yyyy': return $this->get('Y'); break;
			case 'mm': return $this->get('m'); break;
			case 'dd': return $this->get('d'); break;

			case 'long':
			case 'dmyhm':
				return $this->get('d-m-y@H:i:s');
				break;

			case 'short':
			case 'dmhm':
				return $this->get('d-m@H:i');
				break;
		}


	}
	//>
	//}}}
	//{{{ __toString()
	//<[__toString()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function __toString()
	{
		return $this->get('Y-m-d H:i:s');
	}
	//>
	//}}}
	//{{{ date_diff($d1, $d2)
	//<[date_diff()]
	/**
	 * @access  public
	 * @param   Date|int|string  $d1
	 * @param   Date|int|string  $d2
	 * @return  string
	 */
	public function date_diff($d1, $d2)
	{

		$d1 = ($d1 instanceof Date) ?
			$d1->get_timestamp() :
			(is_string($d1) ? strtotime($d1) : (int) $d1);

		$d2 = ($d2 instanceof Date) ? 
			$d2->get_timestamp() :
			(is_string($d2) ? strtotime($d2) : (int) $d2);

		$diff_secs = abs($d1 - $d2);
		$base_year = min(date("Y", $d1), date("Y", $d2));

		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);

		return array(
			"from" => date('r', $d1),
			"to" => date('r', $d2),
			"years" => date("Y", $diff) - $base_year,
			"months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) -1,
			"months" => date("n", $diff) -1,
			"days_total" => floor($diff_secs / (3600 * 24)),
			"days" => date("j", $diff),
			"hours_total" => floor($diff_secs / 3600),
			"hours" => date("G", $diff),
			"minutes_total" => floor($diff_secs / 60),
			"minutes" => (int) date("i", $diff),
			"seconds_total" => $diff_secs,
			"seconds" => (int) date("s", $diff)
		);

	}
	//>
	//}}}
	//{{{ get($format=null)
	//<[get()]
	/**
	 * @access public
	 * @param string $format 
	 * @return string
	 */
	public function get($format=null)
	{
		
		if (is_null($format))
		{
			$format = 'Y-m-d H:i:s';
		}

		return date($format, $this->_timestamp);

	}
	//>
	//}}}
	//{{{ get_day()
	//<[get_day()]
	/**
	 * @access public
	 * @return string
	 */
	public function get_day()
	{
		return date('j', $this->_timestamp);
	}
	//>
	//}}}
	//{{{ get_days_expired()
	//<[get_days_expired()]
	/**
	 * @access  public
	 * @return  int
	 */
	public function get_days_expired()
	{
		$diff = $this->get_now_diff();

		if ($diff >= 0)
			return 0;

		return intval(-$diff / 86400); # 86400 = 24*60*60
	}
	//>
	//}}}
	//{{{ get_year()
	//<[get_year()]
	/**
	 * @access public
	 * @return string
	 */
	public function get_year()
	{
		return date('Y', $this->_timestamp);
	}
	//>
	//}}}
	//{{{ get_month()
	//<[get_month()]
	/**
	 * @access public
	 * @return string
	 */
	public function get_month()
	{
		return date('n', $this->_timestamp);
	}
	//>
	//}}}
	//{{{ get_str_month($short=false)
	//<[get_str_month()]
	/**
	 * @access public
	 * @return string
	 */
	public function get_str_month($short=false)
	{
		$m = date('n', $this->_timestamp);
		$tpl = View::tpl();
		$months = $tpl->get('_date_months');
		return ($short) ? substr($months[$m-1], 0, 3) : $months[$m-1];
	}
	//>
	//}}}
	//{{{ get_hour()
	//<[get_hour()]
	/**
	 * @access public
	 * @return string
	 */
	public function get_hour()
	{
		return date('H', $this->_timestamp);
	}
	//>
	//}}}
	//{{{ get_minute()
	//<[get_minute()]
	/**
	 * @access public
	 * @return string
	 */
	public function get_minute()
	{
		return date('i', $this->_timestamp);
	}
	//>
	//}}}
	//{{{ get_second()
	//<[get_second()]
	/**
	 * @access public
	 * @return string
	 */
	public function get_second()
	{
		return date('s', $this->_timestamp);
	}
	//>
	//}}}
	//{{{ get_now_diff()
	//<[get_now_diff()]
	/**
	 * @access public
	 * @return string
	 */
	public function get_now_diff()
	{
		return ($this->_timestamp - time());
	}
	//>
	//}}}
	//{{{ get_timestamp()
	//<[get_timestamp()]
	/**
	 * @access public
	 * @return int
	 */
	public function get_timestamp()
	{
		return $this->_timestamp;
	}
	//>
	//}}}
	//{{{ set($str_date)
	//<[set()]
	/**
	 * @access public
	 * @param string $str_date
	 * @return void
	 */
	public function set($str_date)
	{
		$this->set_timestamp(strtotime($str_date));
	}
	//>
	//}}}
	//{{{ set_timestamp($_timestamp)
	//<[set_timestamp()]
	/**
	 * @access public
	 * @param int $_timestamp
	 */
	public function set_timestamp($_timestamp)
	{
		$this->_timestamp = $_timestamp;
	}
	//>
	//}}}
	//{{{ is_expired()
	//<[is_expired()]
	/**
	 * @access  public
	 * @return  bool
	 */
	public function is_expired()
	{
		return ($this->get_now_diff() <= -86400); # 86400 = 24*60*60
	}
	//>
	//}}}

	// ------------------------
	// INTERFACE IMPLEMENTATION
	// ------------------------

	//{{{ quote()
	//<[quote()]
	/**
	 * From iDBEntity
	 *
	 * @access  public
	 * @return  string
	 */
	public function quote()
	{

		return "'" . $this->get('Y-m-d H:i:s') . "'";

	}
	//>
	//}}}
	//{{{ can_be_deleted()
	//<[can_be_deleted()]
	/**
	 * @access  public
	 * @return  bool
	 */
	public function can_be_deleted()
	{
		return true;
	}
	//>
	//}}}

}
?>
