<?php
namespace Rost\View;

/**
* PHP template file renderer.
*/
class Renderer
{
	/**
	* @var string[] A stack with PHP template filenames.
	*/
	static protected $filenameStack = [];
	
	/**
	* Renders the PHP template file and returns the content.
	* 
	* @param string $filename
	* @param mixed[] $variables
	* @return string
	*/
	static function Render($filename, $variables)
	{
		static::$filenameStack[] = $filename;
		unset($filename);
		
		if(isset($variables['variables']))
		{
			extract($variables, EXTR_OVERWRITE);
		}
		else
		{
			extract($variables, EXTR_OVERWRITE);
			unset($variables);
		}
		
		try
		{
			ob_start();
			require end(static::$filenameStack);
			$content = ob_get_clean();
		}
		catch(\Exception $exception)
		{
			ob_end_clean();
			throw $exception;
		}
		array_pop(static::$filenameStack);
		return $content;
	}
}

