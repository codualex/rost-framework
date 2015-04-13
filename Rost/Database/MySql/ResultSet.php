<?php
namespace Rost\Database\MySql;

use IteratorAggregate;

/**
* Respesents a result set from the successfull database query.
* It should be used to fetch rows. Though the rows can be fetched only once.
*/
class ResultSet implements IteratorAggregate
{
	/**
	* @var mysqli_result 
	*/
	protected $mysqliResult;

	/**
	* Constructs the object.
	* 
	* @param mysqli_result $mysqliResult
	*/
	function __construct($mysqliResult)
	{
		$this->mysqliResult = $mysqliResult;
	}

	/**
	* Fetches the next row. Returns null if there are no more rows.
	* 
	* @return (string|null)[]|null
	*/
	function FetchRow()
	{
		return $this->mysqliResult->fetch_assoc();
	}

	/**
	* Returns the number of rows in the result set. 
	* 
	* @return int
	*/
	function Count($result)
	{
		return $this->mysqliResult->num_rows;
	}

	/**
	* Frees the memory associated with the result set. 
	*/
	function Free()
	{
		$this->mysqliResult->free();
	}

	/**
	* Implements a method of the IteratorAggregate interface.
	* This makes the object traversable using foreach.
	* 
	* @return Traversable
	*/
	function GetIterator()
	{
		return $this->mysqliResult;
	}
}