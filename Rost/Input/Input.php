<?php
namespace Rost\Input;

use Rost\Input\Scheme\InputScheme;
use Rost\Input\Scheme\ValueScheme;
use Rost\Validation\ViolationList;

/**
* The input object lets an application to work with data coming from a user
* in a safe and convenient way. It contains specification about what value names
* to expect, how to filter them and validate. This specification should be provided
* to the constructor.
*/
class Input
{
	/**
	* @var InputScheme
	*/
	protected $inputScheme;
	
	/**
	* @var mixed[]
	*/
	protected $values = [];
	
	/**
	* @var mixed[]
	*/
	protected $filteredValues = [];

	/**
	* @var ViolationList
	*/
	protected $violationList;

	/**
	* Creates a new object, initialises it with the input scheme
	* that should be used for the input data. 
	* 	
	* @param InputScheme $inputSchema
	*/
	function __construct($inputSchema)
	{
		$this->inputScheme = $inputSchema;
		$this->violationList = new ViolationList();
	}
	
	/**
	* Selects value names which should be involved in all ongoing group operations.
	* This allows to validate or return a subset of the input data.
	* 
	* @param string[] $names
	*/
	function SelectValues(array $names)
	{
		$this->inputScheme->ActivateValueSchemes($names);	
	}
	
	/**
	* Selects all values which present in the input scheme
	* for ongoing group operations.
	*/
	function SelectAllValues()
	{
		$this->inputScheme->ActivateAllValueSchemes();
	}

	/**
	* Sets the input data to work with.
	* 
	* @param mixed[] $values
	*/
	function SetValues(array $values)
	{
		$this->values = $values;
		$this->filteredValues = [];
	}
	
	/**
	* Merges more values in addition to the existing input data data.
	* 
	* @param mixed[] $values
	*/
	function MergeValues(array $values)
	{
		$this->values = array_merge($this->values, $values);
		$this->filteredValues = [];
	}
	
	/**
	* Sets a single input value to work with.
	* 
	* @param string $name
	* @param mixed $value
	*/
	function SetValue($name, $value)
	{
		$this->values[$name] = $value;
		unset($this->filteredValues[$name]);
	}

	/**
	* Returns an unmodified value by the name.
	* The name should be known to the input scheme and should be selected as active.
	* 
	* @param string $name
	* @return mixed
	*/
	function GetValue($name)
	{
		$valueScheme = $this->inputScheme->GetActiveValueScheme($name);
		return $this->GetValueUsingScheme($name, $valueScheme);
	}
	
	/**
	* Returns all unmodified values which are known
	* to the input scheme and selected as active.
	* 
	* @return mixed[]
	*/
	function GetValues()
	{
		$values = [];
		foreach($this->inputScheme->GetActiveValueSchemes() as $name => $valueScheme)
		{
			$values[$name] = $this->GetValueUsingScheme($name, $valueScheme);
		}
		return $values;
	}
	
	/**
	* Returns an unmodified value by the name and the value scheme.
	* 
	* @param string $name
	* @param ValueScheme $valueScheme
	* @return mixed
	*/
	protected function GetValueUsingScheme($name, $valueScheme)
	{
		if(array_key_exists($name, $this->values))
		{
			return $this->values[$name];
		}
		return $valueScheme->GetDefaultValue();
	}
	
	/**
	* Returns a filtered value by the name, based on the input scheme.
	* The name should be known to the input scheme and should be selected as active.
	* 
	* @param string $name
	* @return mixed
	*/
	function GetFilteredValue($name)
	{
		$valueScheme = $this->inputScheme->GetActiveValueScheme($name);
		return $this->GetFilteredValueUsingScheme($name, $valueScheme);
	}
	
	/**
	* Returns all filtered values which are known
	* to the input scheme and selected as active.
	* 
	* @return mixed[]
	*/
	function GetFilteredValues()
	{
		$values = [];
		foreach($this->inputScheme->GetActiveValueSchemes() as $name => $valueScheme)
		{
			$values[$name] = $this->GetFilteredValueUsingScheme($name, $valueScheme);
		}
		return $values;
	}
	
	/**
	* Returns a filtered value by the name and the value scheme.
	* 
	* @param string $name
	* @param ValueScheme $valueScheme
	* @return mixed
	*/
	protected function GetFilteredValueUsingScheme($name, $valueScheme)
	{
		if(array_key_exists($name, $this->filteredValues))
		{
			return $this->filteredValues[$name];
		}
		if(array_key_exists($name, $this->values))
		{
			$this->filteredValues[$name] = $valueScheme->Filter($this->values[$name]);
			return $this->filteredValues[$name];
		}
		return $valueScheme->GetDefaultValue();
	}

	/**
	* Validates all selected avlues in the input. Returns true the values are
	* valid according to the input scheme, return false otherwise.
	* Fills the internal list by violations happened during the input validation.
	* 
	* @return bool
	*/
	function Validate()
	{
		$this->violationList->Clear();
		$propertyPath = $this->violationList->GetBasePropertyPath();
		$result = true;
		
		foreach($this->inputScheme->GetActiveValueSchemes() as $name => $valueScheme)
		{
			$propertyPath->PushSegment($name);
			$result = $this->ValidateValue($name, $valueScheme) && $result;
			$propertyPath->PopSegment();
		}
		return $result;
	}
	
	/**
	* Validates a single value based on the value scheme, returns true if the value is
	* valid according to the value scheme, return false otherwise.
	* Adds a violation to the internal list if it happened during the validation.
	* 
	* @return bool
	*/
	protected function ValidateValue($name, $valueScheme)
	{
		if(!array_key_exists($name, $this->values))
		{
			return $valueScheme->ValidateMissingValue($this->violationList);;
		}
		$filteredValue = $this->GetFilteredValueUsingScheme($name, $valueScheme);
		return $valueScheme->Validate($filteredValue, $this->violationList);
	}
	
	/**
	* Returns a ViolationList object that contains violations
	* happened during the last validation.
	* 
	* @return ViolationList
	*/
	function GetViolationList()
	{
		return $this->violationList;
	}
}
