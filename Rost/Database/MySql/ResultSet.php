<?php
namespace Rost\Database\MySql;

/**
* Respesents a result set from the successfull database query.
* It should be used to fetch rows. Though the rows can be fetched only once.
* 
* @todo Implement Iterator interface.
*/
class ResultSet
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
	* @return StdObject|null
	*/
	function FetchRow()
	{
		return $this->mysqliResult->fetch_object();
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
}