<?php
namespace Rost\Validation;

/**
* A property path is the data structure for describing a unique path to the value
* that the validator is currently validating in a complex object graph or array.
* Internally the property path is represented by a list of segments. Each segment
* is a property name or an array key to go deeper in the data. 
* 
* For example, take the following property:
* $books[37]->authors[2]->name;
* 
* The corresponding property path object is:
* $path = new PropertyPath;
* $path->SetSegments(['37', 'authors', '2', 'name']);
*/
class PropertyPath
{
	/**
	* @var string[]
	*/
	protected $segments = [];
	
	/**
	* Initializes the property path by an array of segments.
	* 
	* @param string[] $segments
	*/
	function SetSegments(array $segments)
	{
		$this->segments = $segments;
	}
	
	/**
	* Returns an array of all segments of the property path.
	* 
	* @return string[]
	*/
	function GetSegments()
	{
		return $this->segments;
	}
	
	/**
	* Adds the segment to the end of the property path.
	* 
	* @param string $segment
	*/
	function PushSegment($segment)
	{
		$this->segments[] = $segment;
	}
	
	/**
	* Removes the last segment from the property path.
	* Returns the removed segment.
	* 
	* @return string
	*/
	function PopSegment()
	{
		return array_pop($this->segments);
	}
	
	/**
	* Converts the property path to a string by concatenating all segments
	* one after another prepended by the slash each. An empty property path
	* will return an empty string.
	* 
	* Optional extra segments will be appended to the end of the current property path.
	* This works like a relative path. 
	*
	* @param string[] $extraSegments 
	* @return string
	*/
	function ToString(array $extraSegments = [])
	{
		$segments = array_merge($this->segments, $extraSegments);
		return $segments ? '/' . implode('/', $segments) : '';
	}
}
