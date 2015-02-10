<?php
namespace Rost\Validation\Validator;

use Rost\Validation\ViolationList;
use Rost\Validation\Violation;

/**
* @todo Write a good description for this validator.
*/
class NotEmpty extends AbstractValidator
{
	/**
	* @var string Validation failure message template.
	*/
	protected $messageTemplate = 'The input can not be empty.';

	/**
	* Constructs a new object.
	* 
	* You can use {value} placeholder in the message template.
	*
	* @param string $messageTemplate Optional validation failure message template.
	*/
	function __construct($messageTemplate = null)
	{
		if($messageTemplate)
		{
			$this->messageTemplate = $messageTemplate;
		}
	}

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
	*/
	function Validate($value, $violations = null)
	{
		if(is_null($value))
		{
			$parameters = [
				'value' => $value,
			];
			$this->TriggerViolation($violations, $parameters);
			return false;
		}
		return true;
	}
}
