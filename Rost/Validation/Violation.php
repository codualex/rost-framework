<?php
namespace Rost\Validation;

/**
* A violation that happened during validation. It stores a message
* explaining the failed validation.
*/
class Violation
{
	/**
	* @var string
	*/
	protected $messageTemplate;
	
	/**
	* @var string
	*/
	protected $message;
	
	/**
	* @var string[]
	*/
	protected $parameters = [];

	/**
	* Constructs a new object.
	* 
	* @param string $messageTemplate A violation message template with placeholders.
	* @param string[] $parameters An optional array of name / value pairs to fill in the placeholders.
	*/
	function __construct($messageTemplate, $parameters = [])
	{
		$this->messageTemplate = $messageTemplate;
		$this->parameters = $parameters;
	}
	
	/**
	* Returns the violation message template. The template contains placeholders
	* for the parameters returned by GetMessageParameters. Typically you need this
	* template and parameters to a translation engine.
	* 
	* Example: 'The title is invalid. It should have less than {limit} characters.'
	* 
	* @return string
	*/
	function GetMessageTemplate()
	{
		return $this->messageTemplate;
	}
	
	/**
	* Returns the violation message.
	*
	* @return string
	*/
	function GetMessage()
	{
		if($this->message === null)
		{
			$this->message = $this->messageTemplate;
			foreach($this->parameters as $name => $value)
			{
				$this->message = str_replace("{{$name}}", (string)$value, $this->message);
			}
		}
		return $this->message;
	}

	/**
	* Returns the parameters to be inserted into the violation message template.
	* 
	* @return string[]
	*/
	function GetParameters()
	{
		return $this->parameters;
	}
}
