<?php
namespace Rost\Validation\Validator;

use Rost\Validation\Violation;

/**
* An abstract implementation of ValidatorInterface. A base for all validators.
*/
abstract class AbstractValidator implements ValidatorInterface
{
	/**
	* @var string Validation failure message template.
	*/
	protected $messageTemplate = 'The input is invalid.';

	/**
	* Creates a violation object and adds it to the violation list. Initializes
	* the violation by the message template and the given parameters. Parameters should
	* contain enough data to replace all placeholders in the template.
	* 
	* Does nothing if the violation list is not provided.
	*
	* @param ViolationList|null $violations
	* @param string[] $parameters
	* @return string
	*/
	protected function TriggerViolation($violations, $parameters)
	{
		if($violations)
		{
			$violation = new Violation($this->messageTemplate, $parameters);
			$violations->SetViolation($violation);
		}
	}
}
