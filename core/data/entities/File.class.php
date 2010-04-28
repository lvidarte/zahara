<?php
import('core.data.entities.DBEntity');
import('core.data.DBObject');

/**
 * Clase File
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: File.class.php 105 2009-11-26 13:25:01Z xleo $
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package     model
 * @subpackage  entities
 */
class File extends DBEntity
{

	/**
	 * @access  private
	 * @var     string
	 */
	protected $_table = 'files';

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ __construct( &$values=null )
	//<[__construct()]
	/**
	 * @access  public
	 * @param   array   $values
	 * @return  void
	 */
	public function __construct( &$values=null )
	{
		// Seteo de valores por default
		$this->__init();

		// Seteo valores pasados al constructor
		if ( $values !== null )
			$this->set($values);
	}
	//>
	//}}}
	//{{{ __init()
	//<[__init()]
	/**
	 * @access  protected
	 * @return  void
	 */
	protected function __init() {

		$this->_data = array(

			new DBObject('id_file', 'int:10', null, 'exclude:true', 'pkey:true'),
			new DBObject('uniqid', 'varchar:13'),
			new DBObject('id_user', 'object:User'),
			new DBObject('table_related', 'enum:discoveries,actions,tasks,tickets'),
			new DBObject('id_related', 'int:10'),
			new DBObject('name', 'varchar:255'),
			new DBObject('size', 'int:10'),
			new DBObject('type', 'varchar:127'),
			new DBObject('description', 'text', null, 'null:true'),
			new DBObject('date', 'object:Date', new Date)

		);

	}
	//>
	//}}}
	//{{{ create_dir()
	//<[create_dir()]
	/**
	 * @access public
	 * @return string
	 */
	private function create_dir()
	{
		$upload_dir = Config::appBaseDir . Config::appUploadsBaseDir;

		if (!is_dir($upload_dir) || !is_writable($upload_dir))
			return null;

		// Year
		$upload_dir .= Config::appDirSep . $this->date->get('Y');

		if (!is_dir($upload_dir))
			mkdir($upload_dir);

		// Month
		$upload_dir .= Config::appDirSep . $this->date->get('m');

		if (!is_dir($upload_dir))
			mkdir($upload_dir);

		// Day
		/*
		$upload_dir .= Config::appDirSep . $this->date->get_day();
		
		if (!is_dir($upload_dir))
			mkdir($upload_dir);
	   //*/

		return $upload_dir;
	}
	//>
	//}}}
	//{{{ delete($true=true)
	//<[delete()]
	/**
	 * @access  public
	 * @param   bool    $true  Indica si la operacion debe realizarse o es una simulacion
	 * @return  bool
	 */
	public function delete($true=true)
	{
		// Medida de seguridad para evitar errores:
		// Debe usarse setFromDB() antes de delete()
		if (!$this->id())
			$true = false;

		// control
		#TPL::show(Utils::dump($this));

		if ($this->_rm($true))
		{
			$result = DB::delete($this, $true);

			if ($result instanceof Error)
				$result->death($this);
			else
				return true;
		}
	}
	//>
	//}}}
	//{{{ get_full_path()
	//<[get_full_path()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_full_path()
	{
		$path  = Config::appBaseDir . Config::appUploadsBaseDir;
		$path .= Config::appDirSep . $this->date->get('Y');
		$path .= Config::appDirSep . $this->date->get('m');
		$path .= Config::appDirSep . $this->get_id();

		return $path;
	}
	//>
	//}}}
	//{{{ get_id()
	//<[get_id()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_id()
	{
		if ($this->id_file && $this->uniqid)
			return sprintf("%07d%s", 
				$this->id_file, $this->uniqid
			);
	}
	//>
	//}}}
	//{{{ get_id_related()
	//<[get_id_related()]
	/**
	 * @access  public
	 * @return  Discovery|Action|Task|Ticket
	 */
	public function get_id_related()
	{
		$object = null;
		$class = '';

		switch ($this->table_related)
		{
			case 'discoveries': $class = 'Discovery'; break;
			case 'actions': $class = 'Action'; break;
			case 'tasks': $class = 'Task'; break;
			case 'tickets': $class = 'Ticket'; break;
		}

		if ($class)
		{
			import("model.entities.{$class}");
			$object = new $class;
			$object->set_from_db($this->id_related);
		}

		return $object;
	}
	//>
	//}}}
	//{{{ get_type()
	//<[get_type()]
	/**
	 * @access  public
	 * @return  string
	 */
	public function get_type()
	{
		switch ($this->type)
		{
			// Compress
			case 'application/x-gzip':
			case 'application/x-rar':
			case 'application/zip':
			case 'application/x-zip-compressed':
			case 'application/x-bzip2':
			case 'application/gnutar':
			case 'application/x-compressed':
			case 'application/x-compressed-tar':
				return 'compress';
				break;

			// PDF
			case 'application/pdf':
				return 'pdf';
				break;

			// Image
			case 'image/png':
			case 'image/jpeg':
			case 'image/pjpeg':
			case 'image/gif':
			case 'image/tiff':
			case 'image/bmp':
			case 'image/svg+xml':
				return 'image';
				break;

			// Audio
			case 'audio/mpeg':
			case 'audio/ogg':
			case 'audio/x-ms-wma':
			case 'audio/vnd.rn-realaudio':
			case 'audio/x-wav':
				return 'audio';
				break;

			// Text
			case 'text/plain':
			case 'text/csv':
			case 'text/html':
			case 'text/xml':
				return 'text';
				break;

			// Micro$oft
			case 'application/msword':
				return 'word';
				break;
			case 'application/vnd.ms-excel':
			case 'application/excel':
			case 'application/x-excel':
			case 'application/x-msexcel':
				return 'excel';
				break;
			case 'application/vnd.ms-powerpoint':
			case 'application/powerpoint':
			case 'application/mspowerpoint':
			case 'application/x-mspowerpoint':
				return 'powerpoint';
				break;
				
			// Undefined
			default:
				return 'undefined';
		}
	}
	//>
	//}}}
	//{{{ insert($true=true)
	//<[insert()]
	/**
	 * Funcion que intenta insertar en la BBDD
	 * los datos del objeto.
	 *
	 * @access  public
	 * @param   bool    $true  Indica si la operacion debe realizarse o es una simulacion
	 * @return  void
	 */
	public function insert($true=true) {

		$this->check();

		// Insercion en DB
		$result = DB::insert($this, $true);
		
		if ($result instanceof Error)
			$result->death($this);
		else
		{
			$this->id_file = $result;
			return $result;
		}

	}
	//>
	//}}}
	//{{{ send_to_browser()
	//<[send_to_browser()]
	/**
	 * @access  public
	 * @return  void
	 */
	public function send_to_browser()
	{
		if (file_exists($this->get_full_path()))
		{
			set_time_limit(0);
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false);
			header('Content-Description: File Transfer');
			header('Content-Transfer-Encoding: binary');
			header('Content-Disposition: attachment; filename=' . $this->name);
			header('Content-Type: ' . $this->type);
			header('Content-Length: ' . $this->size);
			#ob_clean();
			#flush();
			readfile($this->get_full_path());
			exit;	
		}
	}
	//>
	//}}}
	//{{{ standarize($name)
	//<[standarize()]
	/**
	 * Metodo que convierte el nombre de los archivos
	 * eliminando espacios, mayusculas y caracteres no alfanumericos.
	 *
	 * @access  public
	 * @param   string   $name
	 * @return  void
	 */
	public function standarize($name)
	{
		$name = strtolower(utf8_decode($name));
		$name = Utils::remove_accents($name);
		$name = preg_replace('/\s/', '_', $name);
		$name = preg_replace('/\[|\]|\{|\}|\'|\"|\(|\)/', '', $name);
		$name = preg_replace('/_+/', '_', $name);

		return $name;
	}
	//>
	//}}}
	//{{{ upload($tmp_name, $true=true)
	//<[upload()]
	/**
	 * @access  public
	 * @param   string  $tmp_name
	 * @param   bool    $true
	 * @return  bool
	 */
	public function upload($tmp_name, $true=true)
	{
		// INSERT
		if ($this->insert($true))
		{
			$dest = sprintf("%s%s%s", 
				$this->create_dir(), Config::appDirSep,
				$this->get_id()
			);

			if (move_uploaded_file($tmp_name, $dest))
				return true;
			else
			{
				$e = new Error(array('_errorFileUpload', $this->name));
				$e->death($this);
			}
		}
		else
			return false;
	}
	//>
	//}}}
	//{{{ _rm($true=true)
	//<[_rm()]
	/**
	 * @access  private
	 * @param   bool     $true
	 * @return  bool
	 */
	private function _rm($true=true) {

		if (!$true)
			return true;

		// Borrado imagen
		if (file_exists($this->get_full_path()))
		{
			if (unlink($this->get_full_path()))
				return true;
			else
			{
				$e = new Error(array(
					'_error_file_remove',
					$this->get_full_path()
				));
				$e->death($this);
			}
		}

		return false;
	}
	//>
	//}}}

}
?>
