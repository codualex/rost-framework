<?php
namespace Rost\Http;

/**
* Response represents an HTTP response with JSON encoded data.
*/
class JsonResponse extends Response
{
	/**
	* Constructs the object and initializes it with arbitrary data
	* encoded into RFC4627-compliant JSON format.
	*
	* @param mixed $data
	* @param int $statusCode An optional HTTP status code (default is 200).
	*/
	function __construct($data, $statusCode = Status::OK)
	{
		parent::__construct($this->ConvertToJson($data), $statusCode);
		$this->GetHeaders()->Set('Content-Type', 'application/json');
	}

	/**
	* Converts arbitrary data into RFC4627-compliant JSON format string.
	*
	* @param mixed $data
	* @return string
	* @throws \InvalidArgumentException if an error occurred during the JSON encoding.
	*/
	protected function ConvertToJson($data)
	{
		$json = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

		$lastErrorCode = json_last_error();
		if($lastErrorCode == JSON_ERROR_NONE)
		{
			return $json;
		}
		throw new \InvalidArgumentException($this->ErrorCodeToMessage($lastErrorCode));
	}

	/**
	* Returns an error message by the JSON encoder error code.
	* 
	* @param int $errorCode
	* @return string
	* @see http://php.net/manual/en/function.json-last-error-msg.php
	*/
	protected function ErrorCodeToMessage($errorCode)
	{
		switch($errorCode)
		{
			case JSON_ERROR_DEPTH:
				return 'Maximum stack depth exceeded.';
			case JSON_ERROR_STATE_MISMATCH:
				return 'Underflow or the modes mismatch.';
			case JSON_ERROR_CTRL_CHAR:
				return 'Unexpected control character found.';
			case JSON_ERROR_SYNTAX:
				return 'Syntax error, malformed JSON.';
			case JSON_ERROR_UTF8:
				return 'Malformed UTF-8 characters, possibly incorrectly encoded.';
			default:
				return 'Unknown error.';
		}
	}
}
