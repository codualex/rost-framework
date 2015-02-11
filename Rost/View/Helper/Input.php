<?php
namespace Rost\View\Helper;

use Rost\View\Helper\AbstractHelper;
use Rost\View\Helper\Escaper;
use Rost\Input\Input as InputObject;

/**
* This helper contains methods to work with the input. 
*/
class Input extends AbstractHelper
{
	/**
	* @var InputObject
	*/
	protected static $input;

	/**
	* Initializes the helper state by the given input instance.
	* Subsequent method calls will be made within its contex.
	*
	* @param InputObject $input
	* @todo Decide if we need to check the input instance type.
	*/
	static function Initialize($input)
	{
		static::$input = $input;
	}

	/**
	* Returns a value from the input by the name.
	* 
	* @param string $name
	* @return mixed
	*/
	static function Value($name)
	{
		return static::$input->GetValue($name);
	}
	
	/**
	* Returns a filtered value from the input by the name.
	* 
	* @param string $name
	* @return mixed
	*/
	static function FilteredValue($name)
	{
		return static::$input->GetFilteredValue($name);
	}
	
	/**
	* Returns a violation message from the last input validation.
	* The property path specifies which input element exactly we are checking.
	* Returns an empty string if there are no violations.
	* 
	* @param string[] $propertyPathSegments
	* @return string
	*/
	static function Error(array $propertyPathSegments)
	{
		$violationList = static::$input->GetViolationList();
		$violation = $violationList->GetViolation($propertyPathSegments);
		if($violation)
		{
			return '<p>' . Escaper::Html($violation->GetMessage()) . '</p>';
		}
		return '';
	}
}

