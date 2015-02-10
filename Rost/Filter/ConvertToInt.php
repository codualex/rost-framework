<?php
namespace Rost\Filter;

class ConvertToInt implements FilterInterface
{
	/**
	* Converts the given value to an integer.
	*
	* @param mixed $value
	* @return int
	* @throws \InvalidArgumentException If the given value is not a scalar.
	*/
    function Filter($value)
    {
    	if(is_scalar($value))
    	{
    		return intval($value);
        }
        throw new \InvalidArgumentException(sprintf(
        	'Could not convert "%s" type to an integer.',
        	is_object($value) ? get_class($value) : gettype($value)
        ));
    }
}
