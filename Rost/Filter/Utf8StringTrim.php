<?php
namespace Rost\Filter;

class Utf8StringTrim implements FilterInterface
{
	/**
	* Returns a trimmed version of the given string.
	*
	* @param string $value
	* @return string
	*/
    function Filter($value)
    {
    	throw new \RuntimeException('This filter is not implemented yet.');
    }
}
