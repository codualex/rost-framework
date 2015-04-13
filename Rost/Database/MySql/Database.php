<?php
namespace Rost\Database\MySql;

/**
* The class is a convenient wrapper around the mysqli extension.
* It runs SQL queries in a safe way because of typed placeholder support. 
* 
* The format of a placeholder: {type:name}.
* Supported placeholder types are:
* 
* i: "integer", should be used for integers;
* f: "float", should be used for floating-point types;
* s: "string", should be used for strings and similar types like the date type;
* ai: "array of integers", forms a set of integers for IN(...) clause;
* af: "array of integers", forms a set of floats for IN(...) clause;
* as: "array of strings", forms a set of strings for IN(...) clause;
* n: "name", should be used for identifiers, like table and field names; 
* r: "raw", should be used to inject raw data without escaping.
* 
* All types are null-aware. If you pass a null value then 'NULL' will be used,
* it won't be converted based on the placeholder type.
*/
class Database
{
	/**
	* @var mysqli
	*/
	protected $connection;
	
	/**
	* @var mixed[]
	*/
	protected $settings;

	/**
	* A temporary copy of query parameters, so that
	* callback methods have an access to them.
	* 
	* @var mixed[]
	*/
	protected $parameters = [];

	/**
	* Constructs the object.
	* 
	* @param mixed[] $settings
	*/
	function __construct($settings)
	{
		$defaultSettings = [
			'host' => null,
			'user' => null,
			'password' => null,
			'name' => null,
			'port' => null,
			'socket' => null,
			'persistent' => false,
			'charset' => 'utf8',
			'options' => []
		];
		$this->settings = array_merge($defaultSettings, $settings);
		
		if($this->settings['persistent'])
		{
			$this->settings['host'] = 'p:' . $this->settings['host'];
		}
	}
	
	/**
	* Connects to the database.
	* 
	* @todo Fix a fatal error when "connect_error" with a message in russian used as an exception text.
	*/
	function Connect()
	{
		$this->connection = new \mysqli();
        $this->connection->init();
        
		foreach($this->settings['options'] as $option => $value)
		{
			$this->connection->options($option, $value);
		}

		@$this->connection->real_connect(
			$this->settings['host'], $this->settings['user'], $this->settings['password'],
			$this->settings['name'], $this->settings['port'], $this->settings['socket']
		);
		if($this->connection->connect_error)
		{
			throw new \RuntimeException(sprintf(
				'%d %s',
				$this->connection->connect_errno,
				$this->connection->connect_error
			));
		}

		if(!$this->connection->set_charset($this->settings['charset']))
		{
			throw new \RuntimeException(sprintf(
				'Could not change the charset (%d %s).',
				$this->connection->errno,
				$this->connection->error
			));
		}
	}
	
	/**
	* Disconnects from a previously connected database.
	*/
	function Disconnect()
	{
		$this->connection->close();
		$this->connection = null;
	}

	/**
	* Returns the number of affected rows in a previous operation.
	* 
	* @return int
	*/
	function GetAffectedRows()
	{
		return $this->connection->affected_rows;
	}

	/**
	* Returns the ID generated by a query on a table with a column having the AUTO_INCREMENT attribute.
	* 
	* @return int
	*/
	function GetInsertId()
	{
		return $this->connection->insert_id;
	}

	/**
	* Performs a query on the database. Returns ResultSet on successful
	* SELECT, SHOW, DESCRIBE or EXPLAIN queries, returns null otherwise.
	* Throws an exception on invalid query.
	* 
	* @param string $sql
	* @param mixed[] $parameters
	* @return null|ResultSet
	* @throws RuntimeException on invalid query.
	*/
	function Query($sql, $parameters = [])
	{
		$sql = $this->PrepareQuery($sql, $parameters);

		$start = microtime(true);
		$result = $this->connection->query($sql);
		$elapsed = microtime(true) - $start;

		if(!$result)
		{
			throw new \RuntimeException(sprintf(
				'%d %s',
				$this->connection->errno,
				$this->connection->error
			));
		}
		if(is_object($result))
		{
			return new ResultSet($result);
		}
		return null;
	}
	
	/**
	* Performs a query on the database and returns an array with fetched rows.
	* 
	* @param string $sql
	* @param mixed[] $parameters
	* @return (string|null)[][]
	*/
	function QueryAll($sql, $parameters = [])
	{
		$resultSet = $this->Query($sql, $parameters);
		if($resultSet)
		{
			return iterator_to_array($resultSet);
		}
		return [];
	}
	
	/**
	* Performs a query on the database and returns the first fetched row,
	* returns null if there is no row to fetch.
	* 
	* @param string $sql
	* @param mixed[] $parameters
	* @return (string|null)[]|null
	*/
	function QueryRow($sql, $parameters = [])
	{
		$resultSet = $this->Query($sql, $parameters);
		if($resultSet)
		{
			return $resultSet->FetchRow();
		}
		return null;
	}
	
	/**
	* Performs a query on the database and returns the first column value
	* from the first fetched row, returns null if there is no row to fetch.
	* 
	* @param string $sql
	* @param mixed[] $parameters
	* @return string|null
	*/
	function QueryValue($sql, $parameters = [])
	{
		$columns = $this->QueryRow($sql, $parameters);
		if($columns)
		{
			return reset($columns);
		}
		return null;
	}

	/**
	* Returns SQL query with all placeholders replaces by the given parameters.
	* Parameters will be escaped based on placeholder types.
	* 
	* @param string $sql
	* @param mixed[] $parameters
	* @return string
	*/
	function PrepareQuery($sql, $parameters = [])
	{
		$this->parameters = $parameters;
		$callback = [$this, 'ProcessPlaceholder'];
		$pattern = "~{([a-z0-9]+):([a-z0-9]+)}~uUi";
		return preg_replace_callback($pattern, $callback, $sql);
	}
	
	/**
	* Processes a single placeholder by escaping and returning its value.
	* Should be used as a callback for preg_replace_callback() function.
	* 
	* @param string[] $matches
	* @return string Escaped value.
	*/
	protected function ProcessPlaceholder($matches)
	{
		$placeholder = $matches[0];
		$type = $matches[1];
		$name = $matches[2];
		
		if(!array_key_exists($name, $this->parameters))
		{
			throw new \Exception(sprintf(
				'Missing a parameter referred in the placeholder %s.',
				$placeholder
			));
		}
		$value = $this->parameters[$name];
		
		switch($type)
		{
			case 'i':
				return $this->EscapeInteger($value);
			case 'f':
				return $this->EscapeFloat($value);
			case 's':
				return $this->EscapeString($value);
			case 'ai':
				return $this->EscapeArrayOfIntegers($value);
			case 'af':
				return $this->EscapeArrayOfFloats($value);
			case 'as':
				return $this->EscapeArrayOfStrings($value);
			case 'n':
				return $this->EscapeIdentifier($value);
			case 'r':
				return $value;
		}
		throw new \Exception(sprintf(
			'Unknown type "%s" referred in the placeholder %s.',
			$type,
			$placeholder
		));
	}

	/**
	* Escapes the value so that it feets a column of the integer type.
	* 
	* @param mixed $value
	* @return string
	*/
	protected function EscapeInteger($value)
	{
		if($value === null)
		{
			return 'NULL';
		}
		return strval(intval($value));
	}
	
	/**
	* Escapes the value so that it feets a column of the float type.
	* 
	* @param mixed $value
	* @return string
	*/
	protected function EscapeFloat($value)
	{
		if($value === null)
		{
			return 'NULL';
		}
		return strval(floatval($value));
	}
	
	/**
	* Escapes the value so that it feets a column of the string type.
	* 
	* @param mixed $value
	* @return string
	*/
	protected function EscapeString($value)
	{
		if($value === null)
		{
			return 'NULL';
		}
		return "'" . $this->connection->real_escape_string($value) . "'";
	}
	
	/**
	* Escapes values in the array and create MySQL set with integers.
	* The set is ready to be used for an IN(...) clause.
	* 
	* @param mixed[] $values
	* @return string
	*/
	protected function EscapeArrayOfIntegers($values)
	{
		if(!is_array($values))
		{
			throw new \InvalidArgumentException(sprintf(
				'The value for "ai" type placeholder should be an array, %s given.',
				gettype($values)
			));
		}
		if(!$values)
		{
			return 'NULL';
		}
		foreach($values as $key => $value)
		{
			$values[$key] = $this->EscapeInteger($value);
		}
		return implode(',', $values);
	}
	
	/**
	* Escapes values in the array and create MySQL set with floats.
	* The set is ready to be used for an IN(...) clause.
	* 
	* @param mixed[] $values
	* @return string
	*/
	protected function EscapeArrayOfFloats($values)
	{
		if(!is_array($values))
		{
			throw new \InvalidArgumentException(sprintf(
				'The value for "af" type placeholder should be an array, %s given.',
				gettype($values)
			));
		}
		if(!$values)
		{
			return 'NULL';
		}
		foreach($values as $key => $value)
		{
			$values[$key] = $this->EscapeFloat($value);
		}
		return implode(',', $values);
	}
	
	/**
	* Escapes values in the array and create MySQL set with string.
	* The set is ready to be used for an IN(...) clause.
	* 
	* @param mixed[] $values
	* @return string
	*/
	protected function EscapeArrayOfString($values)
	{
		if(!is_array($values))
		{
			throw new \InvalidArgumentException(sprintf(
				'The value for "as" type placeholder should be an array, %s given.',
				gettype($values)
			));
		}
		if(!$values)
		{
			return 'NULL';
		}
		foreach($values as $key => $value)
		{
			$values[$key] = $this->EscapeString($value);
		}
		return implode(',', $values);
	}

	/**
	* Escapes the value as MySQL identifier.
	* 
	* @param mixed $value
	* @return string
	*/
	protected function EscapeIdentifier($value)
	{
		if(strlen($value) == 0)
		{
			throw new \InvalidArgumentException('Empty value can not be an identifier.');
		}
		return "`" . str_replace("`", "``", $value) . "`";
	}
}