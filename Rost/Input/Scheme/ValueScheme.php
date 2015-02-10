<?php
namespace Rost\Input\Scheme;

use Rost\Filter\FilterInterface;
use Rost\Validation\Validator\ValidatorInterface;
use Rost\Validation\ViolationList;

/**
* The ValueScheme object represents specification for a single named value in the input
* data set. It holds a chain of filters and a chain of validators to work with the value.
*/
class ValueScheme
{
	/**
	* @var FilterInterface[]
	*/
	protected $filters = [];
	
	/**
	* @var ValidatorInterface[]
	*/
	protected $validators = [];

	/**
	* @var mixed
	*/
	protected $defaultValue = null;

	/**
	* Sets the default value to use when the input data set 
	* does not contain a value linked with this value scheme.
	* 
	* @param mixed $value
	* @return self
	*/
	function SetDefaultValue($value)
	{
		$this->defaultValue = $value;
		return $this;
	}
	
	/**
	* Returns the current default value.
	* 
	* @return mixed
	*/
	function GetDefaultValue()
	{
		return $this->defaultValue;
	}
	
	/**
	* Adds the filter to the chain.
	* 
	* @param FilterInterface $filter
	* @return self
	*/
	function AddFilter(FilterInterface $filter)
	{
		$this->filters[] = $filter;
		return $this;
	}

	/**
	* Adds the validator to the chain.
	* 
	* @param ValidatorInterface $validator
	* @return self
	*/
	function AddValidator(ValidatorInterface $validator)
	{
		$this->validators[] = $validator;
		return $this;
	}

	/**
	* Applies a chain of all filters on the given value and returns a result.
	* 
	* @param mixed $value
	* @return mixed
	*/
	function Filter($value)
	{
		foreach($this->filters as $filter)
		{
			$value = $filter->Filter($value);
		}
		return $value;
	}
	
	/**
	* Checks if the given value is valid according to a chain of validators.
	* 
	* Returns false as soon as any validator in the chain reports about failed validation
	* and adds created Violation object to the violation list.
	* 
	* Returns true the value is valid.
	*
	* @param mixed $value
	* @param ViolationList $violations
	* @return bool
	*/
	function Validate($value, $violations)
	{
		foreach($this->validators as $validator)
		{
			if(!$validator->Validate($value, $violations))
			{
				return false;
			}
		}
		return true;
	}
	
	/**
	* This is a special validation case to validate a missing input value.
	* 
	* Finds NotEmpty validator, forces it to fail, fills the violation list and returns false.
	* Returns true if there is not NotEmpty validator in the chain.
	* 
	* @param ViolationList $violations
	* @return bool
	*/
	function ValidateMissingValue($violations)
	{
		foreach($this->validators as $validator)
		{
			if($validator instanceof Validator\NotEmpty)
			{
				return $validator->Validate(null, $violations);
			}
		}
		return true;
	}
}
