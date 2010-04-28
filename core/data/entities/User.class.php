<?php
import('core.data.entities.DBEntity');
import('core.data.DBObject');

/**
 * Class User
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: User.class.php 131 2010-02-15 13:43:56Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     model
 * @subpackage  entities
 */
class User extends DBEntity
{

	/**
	 * @access  private
	 * @var     string
	 */
	protected $_table = 'users';

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
	 * @access  public
	 * @return  void
	 */
	protected function __init()
	{

		$this->_data = array(

			new DBObject('id_user', 'int:10', null, 'exclude:true', 'pkey:true'),
			new DBObject('id_sector', 'object:Sector'),
			new DBObject('username', 'varchar:20'),
			new DBObject('password', 'varchar:32'),
			new DBObject('name', 'varchar:20'),
			new DBObject('lastname', 'varchar:20'),
			new DBObject('birthday', 'object:Date', null, 'null:true'),
			new DBObject('phone_home', 'varchar:20', null, 'null:true'),
			new DBObject('phone_work', 'varchar:20', null, 'null:true'),
			new DBObject('movil', 'varchar:32', null, 'null:true'),
			new DBObject('email', 'varchar:128', null, 'null:true'),
			new DBObject('reference', 'varchar:40', null, 'null:true'),
			new DBObject('comment', 'varchar:256', null, 'null:true'),
			new DBObject('level_id', 'int:10', 0, 'default:0'),
			new DBObject('last_access', 'object:Date', new Date, 'exclude:true'),
			new DBObject('date', 'object:Date', new Date, 'exclude:true')

		);

		// control
		#TPL::show(Utils::dump($this));

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
		return $this->username;
	}
	//>
	//}}}
	//{{{ insert($true=true)
	//<[insert()]
	/**
	 * Funcion que intenta insertar en la BBDD
	 * los datos del objeto
	 *
	 * @access  public
	 * @param   bool    $true  Indica si la operacion debe realizarse o es una simulacion
	 * @return  int
	 */
	public function insert($true=true)
	{

		$this->check();

		// Insercion en DB
		$result = DB::insert($this, $true);
		
		if ($result instanceof Error)
			$result->death($this);
		else
		{
			$this->id_user = $result;
			return $result;
		}

	}
	//>
	//}}}
	//{{{ update(User $obj, $true=true)
	//<[update()]
	/**
	 * Funcion que hace un update en la BBDD del objeto
	 *
	 * @access  public
	 * @param   User     $obj   El objecto producto desde el cual actualizar
	 * @param   bool     $true  Indica si la operacion debe realizarse o es una simulacion
	 * @return  bool
	 */
	public function update(User $obj, $true=true)
	{

		// ------------------------
		// Seteo nuevas propiedades
		// ------------------------
		//
		$this->id_sector = $obj->id_sector;
		//
		if ($obj->password) $this->password = $obj->password;
		//
		$this->name = $obj->name;
		$this->lastname = $obj->lastname;
		#$this->birthday = $obj->birthday;
		#$this->phone_home = $obj->phone_home;
		$this->phone_work = $obj->phone_work;
		$this->movil = $obj->movil;
		$this->email = $obj->email;
		$this->level_id = $obj->level_id;
		#$this->reference = $obj->reference;
		#$this->comment = $obj->comment;

		// Control de integridad
		$this->check();

		// ------
		// Update
		// ------
		//
		$result = DB::update($this, $true);

		if ($result instanceof Error)
			$result->death($this);
		
		return true;

	}
	//>
	//}}}
	//{{{ delete($true=true)
	//<[delete()]
	/**
	 * Sin parametros, borra sus propios datos de la BBDD.
	 * Sino borra el nodo cuyo id_node corresponda con el pasado por parametro.
	 *
	 * @access  public
	 * @param   bool     $true  Indica si la operacion debe realizarse o es una simulacion
	 * @return  bool
	 */
	public function delete($true=true)
	{

		// Medida de seguridad para evitar errores:
		// Debe usarse set_from_db() antes de delete()
		if ( ! $this->id() )
			$true = false;

		// Borrado del registro
		$result = DB::delete($this, $true);

		// Salida
		if ($result instanceof Error)
			$result->death($this);
		else
			return true;

	}
	//>
	//}}}
	//{{{ exists($field)
	//<[exists()]
	/**
	 * Averigua si un usuario ya existe en la BBDD
	 *
	 * @access  public
	 * @return  bool
	 */
	public function exists($field)
	{

		return DB::check("
			SELECT * FROM {$this->_table}
			WHERE {$field}={$this->quote($field)}"
		);

	}
	//>
	//}}}
	// {{{ set_user(&$row)
	//<[set_user()]
	/**
	 * @access  public
	 * @param   array   $row
	 * @return  void
	 */
	public function set_user(&$row)
	{

		if (is_array($row))
			foreach ($row as $key => $value)
				$this->$key = $value;

	}
	//>
	//}}}
	// {{{ has($level_id)
	//<[has($level_id)]
	/**
	 * Devuelve true si el usuario posee alguno de los
	 * permisos solicitados
	 *
	 * @access  public
	 * @param   string|int  $level_id
	 * @return  bool
	 */
	public function has($level_id)
	{

		if (is_int($level_id))
			return $this->level_id & $level_id;
		elseif (is_string($level_id))
			return $this->level_id & Config::get_value($level_id);
		else
			return false;

	}
	//>
	// }}}
	// {{{ can_view($object)
	//<[can_view($object)]
	/**
	 * @access  public
	 * @param   mixed   $object
	 * @return  bool
	 */
	public function can_view($object)
	{
		// ************************
		// ADMINISTRADOR HALLAZGOS?
		// ************************
		if ($this->has('D_ADMIN'))
			return true;

		// ******
		// BASICO
		// ******
		// puede ver objetos del tipo $object?
		if ($object instanceOf Discovery && 
				!$this->has('D_VIEW'))
			return false;
		//
		if ($object instanceOf Action && (
				!$this->has('A_VIEW') || 
				!$this->can_view($object->get_discovery())))
			return false;
		//
		if ($object instanceOf Task && (
				!$this->has('D_VIEW') ||
				!$this->can_view($object->get_id_related())))
			return false;
		//
		if ($object instanceOf Ticket && (
				!$this->has('T_VIEW') ||
				!$this->can_view($object->get_id_related())))
			return false;
		//
		if ($object instanceOf File &&
			!$this->can_view($object->get_id_related()))
			return false;

		// ***********
		// AUTORIZADOS
		// ***********
        if ( ! $object->item_users instanceOf ItemUsers )
            return true;

		$authorized = $object->item_users->get_by_type('authorized');

		// El objeto no tiene restricciones?
		if (count($authorized) == 0)
			return true;

		// El usuario se encuentra entre los autorizados?
		foreach ($authorized as $user)
			if ($user->id_user == $this->id_user)
				return true;

		//
		return false;
	}
	//>
	// }}}
	// {{{ can_modify($object)
	//<[can_modify($object)]
	/**
	 * @access  public
	 * @param   mixed   $object
	 * @return  bool
	 */
	public function can_modify($object)
	{
		// Requerido poder Ver
		if (!$this->can_view($object))
			return false;

		// *****************************************************
		// REQUERIDO PARA PODER EDITAR:
		// que ni el objeto ni los padres esten cerrados.
		// REQUERIDO PARA PODER RE-ABRIR:
		// que los padres no esten cerrados.
		// *****************************************************
		if ($object instanceOf Discovery)
		{
			if ($object->is_closed())
			{
				// Solo D_ADMIN puede re-abrir
				if (!$this->has('D_ADMIN'))
					return false;
			}

			// **********************************************************
			// Caso especial: Usuario creador de Hallazgo aun no aprobado
			// **********************************************************
			$u = $object->get_user_creator();
			if ($u->id_user == $this->id_user && !$object->is_approved())
				return true;

			// Permisos
			$modify = $this->has('D_MODIFY');
			$admin = $this->has('D_ADMIN');
		}
		elseif ($object instanceOf Action)
		{
			if ($object->is_closed())
			{
				// Solo D_ADMIN y A_ADMIN pueden re-abrir
				if (!$this->has('D_ADMIN') && !$this->has('A_ADMIN'))
					return false;
			}

			// Nada que hacer si el Hallazgo esta cerrado
			if ($object->get_discovery()->is_closed())
				return false;

			// Permisos
			$modify = ($this->has('A_MODIFY') || $this->has('D_ADMIN'));
			$admin = ($this->has('A_ADMIN') || $this->has('D_ADMIN'));
		}
		elseif ($object instanceOf Task)
		{
			// Solo D_ADMIN puede re-abrir
			if ($object->is_closed() && !$this->has('D_ADMIN'))
				return false;

			// Nada que hacer si el Hallazgo esta cerrado
			if ($object->get_discovery()->is_closed())
				return false;

			// Permisos
			$modify = $this->has('D_MODIFY');
			$admin = $this->has('D_ADMIN');
		}
		elseif ($object instanceOf Ticket)
		{
			// Solo D_ADMIN puede re-abrir
			if ($object->is_closed() && !$this->has('D_ADMIN'))
				return false;

			// Relacionado: 
			// Hallazgo / Accion / Disposicion
			$related = $object->get_id_related();

			// Nada que hacer si el relacionado esta cerrado
			if ($related->is_closed())
				return false;

			// Accion - Disposicion
			if (($related instanceOf Action ||
					$related instanceOf Task) &&
					$related->get_discovery()->is_closed())
				return false;

			// Permisos
			$modify = $this->has('T_CREATE');
			$admin = $this->has('T_ADMIN');
		}
		else
			return false;

		// Es admin?
		if ($admin)
			return true;

		// No tiene permisos de modificacion?
		if (!$modify)
			return false;

		// **************************
		// Usuario creador del objeto
		// **************************
		$u = $object->get_user_creator();
		if ($u->id_user == $this->id_user && !$object instanceOf Discovery)
			return $modify;

		// Usuario responsable
		$U = $object->item_users->get_by_type('in_charge');
		foreach ($U as $u)
			if ($u->id_user == $this->id_user)
				return $modify;

		// No puede
		return false;

	}
	//>
	// }}}
	// {{{ can_delete($object)
	//<[can_delete($object)]
	/**
	 * @access  public
	 * @param   mixed   $object
	 * @return  bool
	 */
	public function can_delete($object)
	{
		if ($this->has('D_ADMIN'))
			return true;

		return false;

	}
	//>
	// }}}
	// {{{ is_creator($object)
	//<[is_creator($object)]
	/**
	 * Devuelve true si el usuario es el creador del objeto
	 * pasado por parametro
	 *
	 * @access  public
	 * @param   mixed   $object
	 * @return  bool
	 */
	public function is_creator($object)
	{
		$u = $object->get_user_creator();
		return ($u->id_user == $this->id_user);
	}
	//>
	// }}}
	// {{{ is_in_charge($object)
	//<[is_in_charge($object)]
	/**
	 * Devuelve true si el usuario es uno de los responsables del objeto
	 * pasado por parametro
	 *
	 * @access  public
	 * @param   mixed   $object
	 * @return  bool
	 */
	public function is_in_charge($object)
	{
		$U = $object->item_users->get_by_type('in_charge');

		foreach ($U as $u)
			if ($u->id_user == $this->id_user)
				return true;

		return false;
	}
	//>
	// }}}

}
?>
