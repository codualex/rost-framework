<?php
namespace Rost\Validation\Validator;

use Rost\Validation\ViolationList;
use Rost\Validation\Violation;

/**
* A validator that ensures a string does not have more characters than required.
*/
class StringMaxLength extends AbstractValidator
{
	/**
	* @var string Validation failure message template.
	*/
	protected $messageTemplate = 'The input is more than {limit} characters long.';

	/**
	* @var int
	*/
	protected $maxLength;

	/**
	* Constructs a new object.
	* 
	* You can use {value} and {limit} placeholders in the message template.
	*
	* @param int $maxLength Allowed maximum number of characters.
	* @param string $messageTemplate Optional validation failure message template.
	*/
	function __construct($maxLength, $messageTemplate = null)
	{
		$this->maxLength = $maxLength;
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
		if(strlen($value) > $this->maxLength)
		{
			$parameters = [
				'value' => $value,
				'limit' => $this->maxLength
			];
			$this->TriggerViolation($violations, $parameters);
			return false;
		}
		return true;
	}
}
