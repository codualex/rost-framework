<?php
namespace Rost\Filter;

/**
* The interface for a filter.
*/
interface FilterInterface
{
	/**
	* Returns a filtered/modified value.
	*
	* @param mixed $value
	* @return mixed
	* @throws \InvalidArgumentException If filtering is impossible for the given value.
	*/
	function Filter($value);
}
