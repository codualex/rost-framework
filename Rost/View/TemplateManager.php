<?php
namespace Rost\View;

use Rost\View\Renderer;

/**
* TemplateManager is responsible for resolving and rendering template files.
* Also it holds variables which should be visible in templates.
*/
class TemplateManager
{
	/**
	* @var string
	*/
	protected $directory;
	
	/**
	* @var string
	*/
	protected $extension;
	
	/**
	* @var string
	*/
	protected $encoding;
	
	/**
	* @var mixed[]
	*/
	protected $globalVariables = [];

	/**
	* Constructs the object.
	* 
	* @param string $directory The root template directory.
	* @param string $extension The template file extention.
	* @param string $encoding The template encoding.
	* @throws InvalidArgumentException if the template directory is not accessible.
	*/
	function __construct($directory, $extension, $encoding)
	{
		if(!is_dir($directory))
		{
			throw new \InvalidArgumentException(sprintf(
				'Could not access the template directory: "%s".',
				$directory
			));
		}
		$this->directory = $directory;
		$this->extension = $extension;
		$this->encoding = $encoding;
	}
	
	/**
	* Returns the template encoding.
	* 
	* @return string
	*/
	function GetEncoding()
	{
		return $this->encoding;
	}
	
	/**
	* Sets an array of variables which should be accessible in all templates.
	* 
	* @param mixed[] $variables
	*/
	function SetGlobalVariables(array $variables)
	{
		$this->globalVariables = $variables;
	}

	/**
	* Renders a template and returns it as a string. Optionally accepts
	* extra variables which should be visible inside the template in addition
	* to global template variables which are visible in all templates.
	* 
	* @param string $template
	* @param mixed[] $extraVariables
	* @return string
	*/
	function Render($template, $extraVariables = [])
	{
		$variables = array_replace($this->globalVariables, $extraVariables);
		
		$filename = sprintf('%s/%s.%s', $this->directory, $template, $this->extension);
		if(!file_exists($filename))
		{
			throw new \RuntimeException(sprintf(
				'Could not render "%s" template. The file "%s" does NOT exist.',
				$template,
				$filename
			));
		}
		return Renderer::Render($filename, $variables);
	}
}
