<?php
import('core.HTML');
import('core.Error');
import('core.data.ResultSet');

/**
 * Class DB
 *
 * @author      xleo <lvidarte@gmail.com>
 * @version     $Id: DB.class.php 67 2009-06-17 03:51:48Z xleo $
 * @license     http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @package     core
 * @subpackage  data
 */
class DB
{

	/**
	 * Component
	 * @var     mysqli
	 * @access  private
	 */
	private static $_instance = null;

	/**
	 * Component
	 * @var     string
	 * @access	private
	 */
	private static $_last_query = null;

	// -------------
	// BEGIN METHODS
	// -------------

	//{{{ connect()
	//<[connect()]
	/**
	 * Singleton pattern
	 *
	 * @access  public
	 * @static
	 * @return  mixed
	 */
	public static function connect()
	{
		if (!isset(self::$_instance))
		{
			if (Config::dbEnabled && Config::dbHost && Config::dbUser
				&& Config::dbPass && Config::dbDatabase)
			{
				self::$_instance = new mysqli(
					Config::dbHost, Config::dbUser,
					Config::dbPass, Config::dbDatabase
				);

				/* Check connection */
				if ( mysqli_connect_errno() )
				{
					printf("Connect failed: %s\n", mysqli_connect_error());
					exit();
				}
			}
			else
			{
				printf("Connect failed: DB not enabled or Missing arguments.
					Please check Config.");
				exit();
			}

			#die(self::$_instance->character_set_name());
			self::query("SET NAMES 'utf8'");
		}

		return self::$_instance;
	}
	//>
	//}}}
	//{{{ close()
	//<[close()]
	/**
	 * Close connection
	 *
	 * @access public
	 * @return void
	 */
	public static function close()
	{
		if (isset(self::$_instance))
		{
			self::$_instance->close();
		}
	}
	//>
	//}}}
	//{{{ __clone()
	//<[__clone()]
	/**
	 * Prevent users to clone the instance
	 *
	 * @access public
	 * @return void
	 */
	public function __clone()
	{
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	//>
	//}}}
	//{{{ query($query, $do=true)
	//<[query()]
	/**
	 * Performs a query on the database
	 *
	 * @access  public
	 * @static
	 * @param   string   $query
	 * @param   boolean  $do      hacer?
	 * @return  mixed
	 */
	public static function query($query, $do=true)
	{
		// Plain format
		$plain_query = self::normalize($query);

		// Show query and die
		if ( ! $do )
			TPL::show(Utils::dump($plain_query));
		
		// Log the query
		Registry::add('__QUERIES__', $plain_query);
		
		// Do the query
		if ( $result = self::connect()->query($plain_query) )
		{
			// Save last query 
			self::$_last_query = $plain_query;
			$rs = new ResultSet($result);
			return $rs;
		}
		// Query fail
		else
		{
			$description  = "<p><b>mysql&gt;</b> $plain_query</p>";
			$description .= "<p><b>error&gt;</b> " . self::connect()->error . "</p>";
			$error = new Error('MySQL Error', $description);
			return $error;
		}

	}
	//>
	//}}}
	//{{{ query_row($query)
	//<[query_row()]
	/**
	 * Performs a query on the database and
	 * return the first row.
	 *
	 * @access  public
	 * @static
	 * @param   string  $query
	 * @param   boolean  $do      hacer?
	 * @return  array|null|Error     Array en caso de obtener resultados.
	 *                           		null en caso de no obtener resultados.
	 *                           		Objeto Error en caso de error.
	 */
	public static function query_row($query, $do=true)
	{
		$result = self::query($query, $do);

		if ( $result instanceof Error )
		{
			return $result;
		}

		if ( $result->num_rows() )
		{
			return $result->fetch_row();
		}
		
		return null;
	}
	//>
	//}}}
	//{{{ normalize($query)
	//<[normalize()]
	/**
	 * Normalize a query string
	 *
	 * @access  public
	 * @static
	 * @param   string   $query
	 * @return  string
	 */
	public static function normalize($query)
	{

		// Plain format
		$plain_query = trim($query);
		$plain_query = preg_replace('/\t+/', '', $plain_query);
		$plain_query = preg_replace('/\n/', ' ', $plain_query);
		$plain_query = preg_replace('/\s+/', ' ', $plain_query);

		return $plain_query;

	}
	//>
	//}}}
	//{{{ check($query)
	//<[check()]
	/**
	 * Ejecuta el query y devuelve la cantidad de
	 * registros obtenidos como resultado.
	 * Util antes de un insert,
	 * para saber si algo ya existe en la BBDD.
	 *
	 * Ejemplo, la consulta 
	 *   SELECT email FROM users WHERE email='foo@bar'
	 * deberia devolver 0 como resultado si lo que deseamos
	 * es insertar un nuevo registro con foo@bar como email
	 *
	 * @access  public
	 * @static
	 * @param   string  $query
	 * @return  int
	 */
	public static function check($query)
	{
		
		$result = self::query($query);

		if ( $result instanceof Error )
		{
			return $result;
		}

		return $result->num_rows();
			
	}
	//>
	//}}}
	//{{{ prepare($query)
	//<[prepare()]
	/**
	 * Prepare a SQL statement for execution
	 *
	 * @access  public
	 * @static
	 * @param   string              $query
	 * @return  statement|false
	 */
	public static function prepare($query)
	{
		return self::connect()->prepare($query);
	}
	//>
	//}}}
	//{{{ autocommit($autocommit=true)
	//<[autocommit()]
	/**
	 * Turns on or off auto-commiting database modifications
	 *
	 * @access  public
	 * @static
	 * @param   boolean  $autocommit
	 * @return  boolean
	 */
	public static function autocommit($autocommit=true)
	{
		return self::connect()->autocommit($autocommit);
	}
	//>
	//}}}
	//{{{ commit()
	//<[commit()]
	/**
	 * Commits the current transaction
	 *
	 * @access  public
	 * @static
	 * @return  boolean
	 */
	public static function commit()
	{
		return self::connect()->commit();
	}
	//>
	//}}}
	//{{{ rollback()
	//<[rollback()]
	/**
	 * Rolls back current transaction
	 *
	 * @access  public
	 * @static
	 * @return  boolean
	 */
	public static function rollback()
	{
		return self::connect()->rollback();
	}
	//>
	//}}}
	//{{{ print_query($query, $force_row_keys=false, $return=true)
	//<[print_query()]
	/**
	 * Imprime o devuelve el resultado de una consulta SQL
	 * en una tabla con formato html.
	 * Para ello utiliza la funcion HTML::table()
	 *
	 * @access  public
	 * @static
	 *
	 * @param   string   $query           Contiene la consulta SQL.
	 * @param   bool     $force_row_keys  Se muestran los indices de fila?
	 * @param   bool     $return          Indica si se retorna un string con el
	 *                                    codigo HTML de la tabla, o se imprime. 
	 *
	 * @return  mixed                     Retorna null en caso de no obtener resultados.
	 *                                    La dos opciones restantes son devolver un
	 *                                    un string con el HTML o imprimir la tabla
	 *                                    (devolver nada).
	 */
	public static function print_query($query, $force_row_keys=false, $return=true)
	{

		// ---------------------------
		// Inicializacion de variables
		// ---------------------------
		$html      = new HTML;
		$nullValue = $html->em('null');
		$_query    = nl2br( trim($query) );
		//
		// Seteo los mensajes para los siguientes casos:
		//  a. No se han devuelto resultados.
		//  b. La consulta es erronea.
		$_ = array(
			'no_results' => 'Empty set',
			'error'      => 'Error'
		);


		// ----------------------------------------
		// Consulta a la DB y analisis de resultado
		// ----------------------------------------
		//
		$rs = self::query($query);
		//
		// Hubo error?
		if ( $rs instanceof Error )
		{
			$rows[$_['error']] = $rs->get_message();
		}
		else
		{
			// Hay resultados?
			if ( $rs->num_rows() > 0 )
			{
				// Traigo todas las filas devueltas
				while ( $row = $rs->fetch_row() )
				{
					$rows[] = $row;
				}
			}
			else
			{
				$rows[$_['no_results']] = 0;
			}
		}

		// ---------------
		// return OR print
		// ---------------
		if ($return)
		{

			return $html->table(
				$rows, $_query, 'print_query', 
				$force_row_keys, true, 
				false, false, $nullValue, 
				$return
			);

		}
		else
		{

			$html->_table(
				
				$rows, $_query, 'print_query', 
				$force_row_keys, true, 
				false, false, $nullValue, 
				$return
			);

		}

	}
	//>
	//}}}
	//{{{ insert($obj, $true=true)
	//<[insert()]
	/**
	 * Realiza un INSERT en la BBDD del objeto pasado por referencia
	 *
	 * @access  public
	 * @static
	 *
	 * @param   Object  $obj
	 * @param   bool    $true  Indica si la operacion es verdadera o una simulacion
	 * @return  mixed          Retorna el ID generado al realizar el insert en caso de exito
	 *                         o un objeto Error en caso de error
	 */
	public static function insert($obj, $true=true)
	{

		$comma  = '';
		$values = '';
		$query  = "INSERT INTO " . $obj->table() . " (";

		foreach ($obj as $db_obj)
		{

			if ($db_obj->exclude) continue;
			##if ($db_obj->null && $db_obj->value === null) continue; 
			
			$query  .= $comma . $db_obj->name;
			$values .= $comma . $db_obj->quote();
			$comma   = ', ';

		}

		$query.= ") VALUES ($values)";

		// Control
		if ( ! $true )
			TPL::show($query);

		// *************************
		// Ejecucion de consulta SQL
		// *************************
		$result = self::query($query);

		if ( $result instanceof Error )
		{
			// Retorno objeto Error
			$result->set_message('_error_db_insert');
			#TPL::show(Utils::dump($result));
			return $result;
		}
		else
		{
			// Retorno del ID generado en el insert
			return self::$_instance->insert_id;
		}

	}
	//>
	//}}}
	//{{{ update($obj, $true=true)
	//<[update()]
	/**
	 * Realiza un UPDATE en la BBDD del objeto pasado por referencia
	 *
	 * @access  public
	 * @static
	 *
	 * @param   Object  $obj
	 * @param   bool    $true  Indica si la operacion es verdadera o una simulacion
	 * @return  mixed          Retorna true en caso de exito
	 *                         o un objeto Error en caso de error
	 */
	public static function update($obj, $true=true)
	{

		$comma  = '';
		$values = '';
		$query  = "UPDATE " . $obj->table() . " SET ";

		foreach ($obj as $db_obj)
		{

			if ($db_obj->exclude) continue;
			#if ($db_obj->null && $db_obj->value === null) continue; 
			
			$query  .= $comma . $db_obj->name . '=' . $db_obj->quote();
			$comma   = ', ';

		}

		$query .= " WHERE " . $obj->pkey() . "=" . $obj->id() . " LIMIT 1";

		// Control
		if ( ! $true )
		{
			TPL::show($query);
		}

		$result = self::query($query);

		if ( $result instanceof Error )
		{
			// Retorno objeto Error
			$result->set_message('_error_db_update');
			return $result;
		}
		else
		{
			return true;
		}


	}
	//>
	//}}}
	//{{{ delete($obj, $true=true)
	//<[delete()]
	/**
	 * Borra de la BBDD el objeto pasado por referencia
	 *
	 * @access  public
	 * @static
	 *
	 * @param   Object  $obj
	 * @param   bool    $true  Indica si la operacion es verdadera o una simulacion
	 * @return  mixed          Retorna true en caso de exito
	 *                         o un objeto Error en caso de error
	 */
	public static function delete($obj, $true=true)
	{

		$_query  = "DELETE FROM " . $obj->table() . " ";
		$_query .= "WHERE " . $obj->pkey() . "=" . $obj->id() . " ";
		$_query .= "LIMIT 1";

		// Control
		if ( ! $true )
		{
			TPL::show($_query);
		}

		$result = self::query($_query);

		if ( $result instanceof Error )
		{
			// Retorno objeto error
			$result->set_message('_error_db_delete');
			return $result;
		}
		else
		{
			return true;
		}

	}
	//>
	//}}}
	//{{{ get_select($table, $id, $value, $order=null, $selected=null, $where=null)
	//<[get_select()]
	/**
	 * Obtiene un array con los datos necesarios para crear
	 * un campo de tipo select en un formulario.
	 *
	 * @access  public
	 * @static
	 *
	 * @param   string   $table
	 * @param   string   $id        Campo de la tabla que contiene el valor de A
	 *                              <option value="A">B</option>
	 * @param   string   $value     Campo de la tabla que contiene el texto de B
	 *                              <option value="A">B</option>
	 * @param   string   $order     Campo de la tabla por el cual ordenar el resultado.
	 *                              Si es null se ordena por $value.
	 * @param   string   $selected  ID del elemento que debe mostrarse como seleccionado
	 * @param   string   $where     Condicion WHERE
	 * @return  array
	 */
	public static function get_select($table, $id, $value, $order=null, $selected=null, $where=null)
	{

		$query = "SELECT $id as id, $value as value FROM $table";

		if ($where) $query .= " WHERE $where";

		$query .= " ORDER BY " . ($order ? $order : $value);

		$rs = self::query($query);

		$array = array();
		if ($selected !== null) $array['_selected_'] = "$selected";

		while ( $row = $rs->fetch_row() )
			$array[$row['id']] = $row['value'];

		return $array;

	}
	//>
	//}}}
	//{{{ get_last_query()
	//<[get_last_query()]
	/**
	 * Devuelve el ultimo query realizado
	 *
	 * @access  public
	 * @static
	 * @return  string
	 */
	public static function get_last_query()
	{

		return self::$_last_query;

	}
	//>
	//}}}

}
?>
