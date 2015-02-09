<?php
namespace Rost\Http;

/**
* RedirectResponse represents an HTTP response doing a redirect.
*/
class RedirectResponse extends Response
{
	/**
	* Creates the object with the given URL and the HTTP status code.
	*
	* @param string $url
	* @param int $statusCode
	* @throws \InvalidArgumentException if URL or the status code is incorrect.
	*/
	function __construct($url, $statusCode = Status::FOUND)
	{
		if(empty($url))
		{
			throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
		}
		if(!in_array($statusCode, [Status::MOVED_PERMANENTLY, Status::FOUND]))
		{
			throw new \InvalidArgumentException(sprintf(
				'The given HTTP status code (%s) is not a redirect.',
				$statusCode
			));
		}
		parent::__construct();
		$this->SetStatus($statusCode);
		$this->SetContent($this->GetMetaRedirectHtml($url));
		$this->GetHeaders()->Set('Location', $url);
	}

	/**
	* Returns HTML code that does an instant META redirect.
	*
	* @param string $url
	* @return string
	*/
	protected function GetMetaRedirectHtml($url)
	{
		$template = '<!DOCTYPE html>
			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<meta http-equiv="refresh" content="1;url={url}" />
			        <title>
    					Redirecting to {url}
			        </title>
				</head>
				<body>
					Redirecting to <a href="{url}">{url}</a>.
				</body>
			</html>';
			
		return str_replace('{url}', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'), $template);
	}
}
