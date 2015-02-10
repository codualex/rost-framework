<?php
namespace Rost\Filter;

use DateTime;

class ConvertToDateTime implements FilterInterface
{
	/**
	* Converts the date string to a DateTime object.
	*
	* @param int|string $value
	* @return DateTime
	* @throws \InvalidArgumentException If an invalid date string have been provided.
	*/
    function Filter($value)
    {
		try
		{
			if(is_int($value))
			{
				return new DateTime('@' . $value);
			}
			return new DateTime($value);
		}
		catch(\Exception $exception)
		{
			throw new \InvalidArgumentException(
				'Could not convert a date string into the DateTime object.',
				$exception->getCode(),
				$exception
			);
		}
    }
}
