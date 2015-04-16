<?php
namespace Rost\Router\Route;

use Rost\Router\Route\RouteInterface;
use Rost\Router\Parameters;
use Rost\Http\Request;

class PatternRoute implements RouteInterface
{
	/**
	* @var string
	*/
	protected $pattern;
	
	/**
	* @var string Regex used for matching the route.
	*/
	protected $regex;

	/**
	* @var string[]
	*/
	protected $defaultParameters = [];
	
	/**
	* @var mixed[][]
	*/
	protected $tokens;
	
	/**
	* Map of allowed special chars in URL path segments.
	*
	* PHP's rawurlencode() encodes all chars except "a-zA-Z0-9-._~" according to RFC 3986.
	* But we want to allow some chars to be used in their literal form.
	*/
	protected $encoderCorrectionMap = [
		//The following chars are general delimiters in the URI specification
		//but have only special meaning in the authority component
		//so they can safely be used in the path in unencoded form.
		'%40' => '@',
		'%3A' => ':',
		//These chars have no predefined meaning and can therefore be used literally,
		//so that an applications can use these chars to delimit subcomponents in a path segment
		//without being encoded for better readability.
		'%3B' => ';',
		'%2C' => ',',
		'%3D' => '=',
		'%2B' => '+',
		'%21' => '!',
		'%2A' => '*',
		'%7C' => '|'
	];

	/**
	* Constructs the route based on the given definition.
	* 
	* @param mixed[] $definition
	* @todo Should we show an exception when parameters is not an array?
	*/
	function __construct($definition)
	{
		if(!isset($definition['pattern']))
		{
			throw new \InvalidArgumentException(
				'The route definition must contain a "pattern" key, but it is not there.'
			);
		}
		$this->pattern = $definition['pattern'];
		
		if(isset($definition['parameters']))
		{
			$this->defaultParameters = $definition['parameters'];
		}
		$this->tokens = $this->BuildTokenList($this->pattern);
		$this->ValidateBrackets($this->tokens);
		
		$this->regexp = $this->BuildRegex($this->tokens);
	}

	/**
	* Parses the route pattern and returns array of tokens.
	*
	* @param  string $pattern
	* @return array
	*/
	protected function BuildTokenList($pattern)
	{
		$parts = $this->FindDynamicParts($pattern);
		$currentPosition = 0;
		$tokens = [];
		
		foreach($parts as $part)
		{
			list($type, $content, $offset) = $part;
			
			if($offset > $currentPosition)
			{
				$literalContent = substr($pattern, $currentPosition, $offset - $currentPosition);
				$tokens[] = ['literal', $literalContent];
				$currentPosition = $offset;
			}
			
			if($type == 'variable')
			{
				$variableName = trim($content, '{}');
				$tokens[] = [$type, $variableName];
			}
			elseif($type == 'bracket')
			{
				$bracketType = ($content == '[') ? 'leftBracket' : 'rightBracket';
				$tokens[] = [$bracketType, null];
			}
			$currentPosition += strlen($content);
		}
		
		if($currentPosition < strlen($pattern))
		{
			$content = substr($pattern, $currentPosition);
			$tokens[] = ['literal', $content];
		}
		return $tokens;
	}
		
	/**
	* Parses the pattern and finds offsets of all dynamic parts
	* like variables and brackets.
	*
	* @param  string $pattern
	* @return array
	*/
	protected function FindDynamicParts($pattern)
	{
		$regexp = '#(?<variable>{[A-Za-z0-9]+})|(?<bracket>[\[\]])#';
		if(false === preg_match_all($regexp, $pattern, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE))
		{
			throw new \InvalidArgumentException('Seriously malformed route pattern.');
		}

		$parts = [];
		foreach($matches as $match)
		{
			if(isset($match['variable'][0]) && strlen($match['variable'][0]))
			{
				list($content, $offset) = $match['variable'];
				$parts[] = ['variable', $content, $offset];
			}
			else
			{
				list($content, $offset) = $match['bracket'];
				$parts[] = ['bracket', $content, $offset];
			}
		}
		return $parts;
	}
	
	/**
	* Makes sure brackets are used correctly and balanced.
	*
	* @param string[][] $tokens
	* @throws RuntimeException when brackets are not balanced.
	*/
	protected function ValidateBrackets(array $tokens)
	{
		$level = 0;
		foreach($tokens as $token)
		{
			list($type) = $token;

			switch($type)
			{
				case 'leftBracket':
					$level++;
					break;
				case 'rightBracket':
					$level--;
					break;
			}
			if($level < 0)
			{
				throw new \RuntimeException('Found closing bracket without matching opening bracket.');
			}
		}
		if($level > 0)
		{
			throw new \RuntimeException('Found unbalanced brackets.');
		}
	}
	
	/**
	* Build the matching regex from parsed tokens.
	*
	* @param mixed[][] $tokens
	* @return string
	*/
	protected function BuildRegex(array $tokens)
	{
		$regex = '';
		foreach($tokens as $token)
		{
			list($type, $content) = $token;

			switch($type)
			{
				case 'literal':
					$regex .= preg_quote($content);
					break;
				case 'variable':
					$regex .= '(?<' . $content . '>[^/]+)';
					break;
				case 'leftBracket':
					$regex .= '(?:';
					break;
				case 'rightBracket':
					$regex .= ')?';
					break;
			}
		}
		return '(^' . $regex . '$)';
	}

	/**
	* Matches a given request.
	*
	* @param Request $request
	* @return Parameters|null
	*/
	function Match(Request $request)
	{
		if(preg_match($this->regexp, $request->GetRelativePath(), $matches))
		{
			$parameters = [];
			$expectedParameterNames = $this->GetParameterNames($this->tokens);
			
			foreach($expectedParameterNames as $name)
			{
				if(isset($matches[$name]))
				{
					$parameters[$name] = $this->Decode($matches[$name]);
				}
			}
			return new Parameters(array_merge($this->defaultParameters, $parameters));
		}
		return null;
	}
	
	/**
	* Returns an array of variable names used in the pattern.
	* 
	* @param mixed[][] $tokens
	* @return string[]
	*/
	protected function GetParameterNames($tokens)
	{
		$names = [];
		foreach($tokens as $token)
		{
			list($type, $content) = $token;
			if($type == 'variable')
			{
				$names[] = $content;
			}
		}
		return $names;
	}

	/**
	* Assembles the route into URL.
	*
	* @param string[] $parameters
	* @return string
	*/
	function Assemble($parameters = [])
	{
		$mergedParameters = array_merge($this->defaultParameters, $parameters);
		$path = $this->BuildPath($this->tokens, $mergedParameters);
		
		$knownParameterNames = $this->GetParameterNames($this->tokens);
		$knownParametersAsIndexes = array_fill_keys($knownParameterNames, true);
		$remainingParameters = array_diff_key($parameters, $knownParametersAsIndexes);
		
		if($remainingParameters)
		{
			return $path . '?' . http_build_query($remainingParameters);
		}
		return $path;
	}

	/**
	* Build a path.
	*
	* @param mixed[][] $tokens
	* @param array $parameters
	* @return string
	* @throws \InvalidArgumentException
	*/
	protected function BuildPath(array $tokens, array $parameters)
	{
		$paths = [''];
		$level = 0;
		$token = reset($tokens);
		
		while($token)
		{
			list($type, $content) = $token;

			switch($type)
			{
				case 'literal':
					$paths[$level] .= $content;
					break;
				case 'variable':
					if(isset($parameters[$content]))
					{
						$paths[$level] .= $this->Encode($parameters[$content]);
					}
					else
					{
						if($level == 0)
						{
							throw new \InvalidArgumentException(sprintf('Missing parameter "%s"', $content));
						}
						$this->SkipOptionalPath($tokens);
						$level--;
					}
					break;
				case 'leftBracket':
					$level++;
					$paths[$level] = '';
					break;
				case 'rightBracket':
					$level--;
					$paths[$level] .= $paths[$level + 1];
					break;
			}
			$token = next($tokens);
		}
		return reset($paths);
	}
	
	/**
	* Skips the current optional path by advancing the internal pointer
	* of the array with tokens to the closing bracket.
	* 
	* @param string[][] $tokens
	*/
	protected function SkipOptionalPath(&$tokens)
	{
		$level = 0;
		while($level >= 0)
		{
			list($type) = next($tokens);
			
			switch($type)
			{
				case 'leftBracket':
					$level++;
					break;
				case 'rightBracket':
					$level--;
					break;
			}
		}
	}
	
	/**
	* Encodes a path segment.
	*
	* @param string $value
	* @return string
	*/
	protected function Encode($value)
	{
		return strtr(rawurlencode($value), $this->encoderCorrectionMap);
	}

	/**
	* Decodes a path segment.
	*
	* @param string $value
	* @return string
	*/
	protected function Decode($value)
	{
		return rawurldecode($value);
	}
}
