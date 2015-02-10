<?php
namespace Rost\Validation\Validator;

use Rost\Validation\ViolationList;

/**
* The interface for a validator.
*/
interface ValidatorInterface
{
	/**
	* Checks if the given value is valid according to the validator. Returns
	* true if the value is valid, returns false otherwise.
	* 
	* If an optional instance of ViolationList is provided, the validator adds
	* a violation object to the list. The violation contains a message that explains
	* why the validation failed.
	* 
	* @param mixed $value
	* @param ViolationList|null $violations
	* @return bool
	* @throws \RuntimeException If validation of the given value is impossible.
	*/
	function Validate($value, $violations = null);
}
