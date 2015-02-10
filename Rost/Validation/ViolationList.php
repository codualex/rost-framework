<?php
namespace Rost\Validation;

/**
* A list of violations which happened during validation. Each violation is
* associated with a property path. A property path uniquely specifies a value
* that failed validation in a complex object graph or an array.
*/
class ViolationList
{
	/**
	* @var PropertyPath
	*/
	protected $basePropertyPath;
	
	/**
	* @var Violation[]
	*/
	protected $violations = [];

	/**
	* Constructs a new object.
	*/
	function __construct()
	{
		$this->basePropertyPath = new PropertyPath();
	}
	
	/**
	* Returns the internal base property path.
	* 
	* @return PropertyPath
	*/
	function GetBasePropertyPath()
	{
		return $this->basePropertyPath;
	}
	
	/**
	* Sets the violation at the base property path. Uses optional relative
	* path segments in addition to the base property path to build a new absolute
	* property path for the violation.
	* 
	* @param Violation $violation
	* @param string[] $relativePathSegments
	*/
	function SetViolation($violation, array $relativePathSegments = [])
	{
		$path = $this->basePropertyPath->ToString($relativePathSegments);
		$this->violations[$path] = $violation;
	}

	/**
	* Returns a violation by the base property path. Uses optional relative
	* path segments in addition to the base property path to build a new absolute
	* property path of the violation.
	* 
	* Returns null if there is no violation found by the given property path.
	*
	* @param string[] $relativePathSegments
	* @return Violation|null
	*/
	function GetViolation(array $relativePathSegments = [])
	{
		$path = $this->basePropertyPath->ToString($relativePathSegments);
		if(isset($this->violations[$path]))
		{
			return $this->violations[$path];
		}
		return null;
	}

	/**
	* Returns true is the violation list is empty.
	* 
	* @return bool
	*/
	function IsEmpty()
	{
		return empty($this->violations);
	}
	
	/**
	* Clears all existing violations from the list.
	*/
	function Clear()
	{
		$this->violations = [];
	}
}
