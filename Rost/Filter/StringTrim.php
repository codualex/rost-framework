<?php
namespace Rost\Filter;

class StringTrim implements FilterInterface
{
	/**
	* Returns a trimmed version of the given string.
	*
	* @param string $value
	* @return string
	*/
    function Filter($value)
    {
    	return trim($value);
    }
}
