<?php
namespace Rost\Filter;

class ConvertToBool implements FilterInterface
{
	/**
	* Converts the given value to a boolean.
	*
	* @param mixed $value
	* @return bool
	* @throws \InvalidArgumentException If the given value is not a scalar.
	*/
    function Filter($value)
    {
    	if(is_scalar($value))
    	{
    		return (bool)$value;
        }
        throw new \InvalidArgumentException(sprintf(
        	'Could not convert "%s" type to a boolean.',
        	is_object($value) ? get_class($value) : gettype($value)
        ));
    }
}