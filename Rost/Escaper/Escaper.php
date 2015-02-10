<?php
namespace Rost\Escaper;

/**
* Context specific methods for use in secure output escaping.
* 
* The following class is based on the code from Zend Framework.
* @license http://framework.zend.com/license/new-bsd
* @copyright 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
*/
class Escaper
{
	/**
	* Entity Map mapping Unicode codepoints to any available named HTML entities.
	*
	* While HTML supports far more named entities, the lowest common denominator
	* has become HTML5's XML Serialisation which is restricted to the those named
	* entities that XML supports. Using HTML entities would result in this error:
	* XML Parsing Error: undefined entity
	*
	* @var string[]
	*/
	protected $htmlNamedEntityMap = [
		34 => 'quot', // quotation mark
		38 => 'amp',  // ampersand
		60 => 'lt',   // less-than sign
		62 => 'gt',   // greater-than sign
	];

	/**
	* Current encoding for escaping (lowercased). We convert strings from this encoding
	* into UTF=8, escape, and then convert back to this encoding.
	*
	* @var string
	*/
	protected $encoding;

	/**
	* Holds the value of the special flags passed as second parameter to
	* htmlspecialchars(). We modify these for PHP 5.4 to take advantage
	* of the new ENT_SUBSTITUTE flag for correctly dealing with invalid
	* UTF-8 sequences.
	*
	* @var string
	*/
	protected $htmlSpecialCharsFlags = ENT_QUOTES;

	/**
	* @var callable Escapes matched characters for HTML attribute contexts.
	*/
	protected $htmlAttributeMatcherCallback;

	/**
	* @var callable Escapes matched characters for Javascript contexts.
	*/
	protected $javascriptMatcherCallback;

	/**
	* @var callable Escapes matched characters for CSS Attribute contexts.
	*/
	protected $cssMatcherCallback;

	/**
	* List of all encoding supported by this class.
	*
	* @var string[]
	*/
	protected $supportedEncodings = [
		'iso-8859-1',   'iso8859-1',    'iso-8859-5',   'iso8859-5',
		'iso-8859-15',  'iso8859-15',   'utf-8',        'cp866',
		'ibm866',       '866',          'cp1251',       'windows-1251',
		'win-1251',     '1251',         'cp1252',       'windows-1252',
		'1252',         'koi8-r',       'koi8-ru',      'koi8r',
		'big5',         '950',          'gb2312',       '936',
		'big5-hkscs',   'shift_jis',    'sjis',         'sjis-win',
		'cp932',        '932',          'euc-jp',       'eucjp',
		'eucjp-win',    'macroman'
	];

	/**
	* Constructs the object, sets an encoding of the input data.
	*
	* @param string $encoding
	* @throws \InvalidArgumentException if the encoding is not supported.
	*/
	function __construct($encoding)
	{
		$this->encoding = strtolower($encoding);
		if(!in_array($this->encoding, $this->supportedEncodings))
		{
			throw new \InvalidArgumentException(sprintf(
				'Invalid encoding: "%s". Provide an encoding supported by htmlspecialchars() function.',
				$controllerName
			));
		}
		
		if(defined('ENT_SUBSTITUTE'))
		{
			$this->htmlSpecialCharsFlags |= ENT_SUBSTITUTE;
		}
		
		$this->htmlAttributeMatcherCallback = [$this, 'HtmlAttributeMatcherCallback'];
		$this->javascriptMatcherCallback = [$this, 'JavascriptMatcherCallback'];
		$this->cssMatcherCallback = [$this, 'CssMatcherCallback'];
	}

	/**
	* Escape a string for the HTML Body context where there are very few characters
	* of special meaning. Internally this will use htmlspecialchars().
	*
	* @param string $string
	* @return string
	*/
	function EscapeHtml($string)
	{
		return htmlspecialchars($string, $this->htmlSpecialCharsFlags, $this->encoding);
	}

	/**
	* Escape a string for the HTML Attribute context. We use an extended set of characters
	* to escape that are not covered by htmlspecialchars() to cover cases where an attribute
	* might be unquoted or quoted illegally (e.g. backticks are valid quotes for IE).
	*
	* @param string $string
	* @return string
	*/
	function EscapeHtmlAttribute($string)
	{
		$string = $this->ConvertToUtf8($string);
		if($string === '' || ctype_digit($string))
		{
			return $string;
		}
		$result = preg_replace_callback('/[^a-z0-9,\.\-_]/iSu', $this->htmlAttributeMatcherCallback, $string);
		return $this->ConvertFromUtf8($result);
	}

	/**
	* Escape a string for the Javascript context. This does not use json_encode(). An extended
	* set of characters are escaped beyond ECMAScript's rules for Javascript literal string
	* escaping in order to prevent misinterpretation of Javascript as HTML leading to the
	* injection of special characters and entities. The escaping used should be tolerant
	* of cases where HTML escaping was not applied on top of Javascript escaping correctly.
	* Backslash escaping is not used as it still leaves the escaped character as-is and so
	* is not useful in a HTML context.
	*
	* @param string $string
	* @return string
	*/
	function EscapeJavascript($string)
	{
		$string = $this->ConvertToUtf8($string);
		if($string === '' || ctype_digit($string))
		{
			return $string;
		}
		$result = preg_replace_callback('/[^a-z0-9,\._]/iSu', $this->javascriptMatcherCallback, $string);
		return $this->ConvertFromUtf8($result);
	}

	/**
	* Escapes a string for the URI or Parameter contexts. This should not be used to escape
	* an entire URI - only a subcomponent being inserted. The function is a simple proxy
	* to rawurlencode() which now implements RFC 3986 since PHP 5.3 completely.
	*
	* @param string $string
	* @return string
	*/
	function EscapeUrl($string)
	{
		return rawurlencode($string);
	}

	/**
	* Escapes a string for the CSS context. CSS escaping can be applied to any string being
	* inserted into CSS and escapes everything except alphanumerics.
	*
	* @param string $string
	* @return string
	*/
	function EscapeCss($string)
	{
		$string = $this->ConvertToUtf8($string);
		if($string === '' || ctype_digit($string))
		{
			return $string;
		}
		$result = preg_replace_callback('/[^a-z0-9]/iSu', $this->cssMatcherCallback, $string);
		return $this->ConvertFromUtf8($result);
	}

	/**
	* Callback function for preg_replace_callback that applies
	* HTML attribute escaping to all matches.
	*
	* @param array $matches
	* @return string
	*/
	protected function HtmlAttributeMatcherCallback($matches)
	{
		$char = $matches[0];
		$ord = ord($char);

		/**
		* The following replaces characters undefined in HTML with the
		* hex entity for the Unicode replacement character.
		*/
		if(($ord <= 0x1f && $char != "\t" && $char != "\n" && $char != "\r") || ($ord >= 0x7f && $ord <= 0x9f))
		{
			return '&#xFFFD;';
		}

		/**
		* Check if the current character to escape has a name entity we should
		* replace it with while grabbing the integer value of the character.
		*/
		if(strlen($char) > 1)
		{
			$char = $this->ConvertEncoding($char, 'UTF-16BE', 'UTF-8');
		}

		$hex = bin2hex($char);
		$ord = hexdec($hex);
		if(isset($this->htmlNamedEntityMap[$ord]))
		{
			return '&' . $this->htmlNamedEntityMap[$ord] . ';';
		}

		/**
		* Per OWASP recommendations, we'll use upper hex entities
		* for any other characters where a named entity does not exist.
		*/
		if($ord > 255)
		{
			return sprintf('&#x%04X;', $ord);
		}
		return sprintf('&#x%02X;', $ord);
	}

	/**
	* Callback function for preg_replace_callback that applies
	* Javascript escaping to all matches.
	*
	* @param array $matches
	* @return string
	*/
	protected function JavascriptMatcherCallback($matches)
	{
		$char = $matches[0];
		if(strlen($char) == 1)
		{
			return sprintf('\\x%02X', ord($char));
		}
		$char = $this->ConvertEncoding($char, 'UTF-16BE', 'UTF-8');
		return sprintf('\\u%04s', strtoupper(bin2hex($char)));
	}

	/**
	* Callback function for preg_replace_callback that applies
	* CSS escaping to all matches.
	*
	* @param array $matches
	* @return string
	*/
	protected function CssMatcherCallback($matches)
	{
		$char = $matches[0];
		if(strlen($char) == 1)
		{
			$ord = ord($char);
		}
		else
		{
			$char = $this->ConvertEncoding($char, 'UTF-16BE', 'UTF-8');
			$ord = hexdec(bin2hex($char));
		}
		return sprintf('\\%X ', $ord);
	}

	/**
	* Converts a string to UTF-8 from the base encoding.
	*
	* @param string $string
	* @return string
	* @throws \RuntimeException
	*/
	protected function ConvertToUtf8($string)
	{
		if($this->encoding === 'utf-8')
		{
			$result = $string;
		}
		else
		{
			$result = $this->ConvertEncoding($string, 'UTF-8', $this->encoding);
		}

		if(!$this->IsUtf8($result))
		{
			throw new \RuntimeException(sprintf(
				'String to be escaped was not valid UTF-8 or could not be converted: %s.',
				$result
			));
		}
		return $result;
	}

	/**
	* Converts a string from UTF-8 to the base encoding.
	* 
	* @param string $string
	* @return string
	*/
	protected function ConvertFromUtf8($string)
	{
		if($this->encoding === 'utf-8')
		{
			return $string;
		}
		return $this->ConvertEncoding($string, $this->encoding, 'UTF-8');
	}

	/**
	* Checks if a given string appears to be valid UTF-8 or not.
	*
	* @param string $string
	* @return bool
	*/
	protected function IsUtf8($string)
	{
		return ($string === '' || preg_match('/^./su', $string));
	}

	/**
	* Converts a string from one encoding to another. It uses iconv or mb_convert_encoding
	* if they exist or throws exception where neither is available.
	*
	* @param string $string
	* @param string $to
	* @param string $from
	* @return string
	* @throws \RuntimeException
	*/
	protected function ConvertEncoding($string, $to, $from)
	{
		if(function_exists('mb_convert_encoding'))
		{
			return mb_convert_encoding($string, $to, $from);
		}
		elseif(function_exists('iconv'))
		{
			return iconv($from, $to, $string);
		}
		throw new \RuntimeException('No suitable convert encoding function (use UTF-8 as your encoding or install the iconv or mbstring extension).');
	}
	
}
