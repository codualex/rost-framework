<?php
namespace Tests\Url;

use Rost\Url\Url;
use Rost\Url\Query;

class UrlTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$url = new Url();
		$this->assertEquals('', $url);
		$url = new Url('');
		$this->assertEquals('', $url);
		$url = new Url('http://domain.com/path?p=v#fragment');
		$this->assertEquals('http://domain.com/path?p=v#fragment', $url);
	}
	
	public function testSetScheme()
	{
		$url = new Url('http://domain.com');
		$url->SetScheme('ftp');
		$this->assertEquals('ftp://domain.com', $url);
	}
	
	public function testGetScheme()
	{
		$url = new Url('http://domain.com');
		$this->assertEquals('http', $url->GetScheme());
	}
	
	public function testSetHost()
	{
		$url = new Url('http://domain.com/path');
		$url->SetHost('domain2.com');
		$this->assertEquals('http://domain2.com/path', $url);
	}
	
	public function testGetHost()
	{
		$url = new Url('http://domain.com/path');
		$this->assertEquals('domain.com', $url->GetHost());
	}
		
	public function testSetPort()
	{
		$url = new Url('http://domain.com:1000/path');
		$url->SetPort(2000);
		$this->assertEquals('http://domain.com:2000/path', $url);
	}
	
	public function testGetPort()
	{
		$url = new Url('http://domain.com:1000/path');
		$this->assertEquals(1000, $url->GetPort());
	}
		
	public function testSetUser()
	{
		$url = new Url('http://user1@domain.com/path');
		$url->SetUser('user2');
		$this->assertEquals('http://user2@domain.com/path', $url->ToString());
	}
	
	public function testGetUser()
	{
		$url = new Url('http://user@domain.com/path');
		$this->assertEquals('user', $url->GetUser());
	}
	
	public function testSetPassword()
	{
		$url = new Url('http://user:password1@domain.com/path');
		$url->SetPassword('password2');
		$this->assertEquals('http://user:password2@domain.com/path', $url->ToString());
	}
	
	public function testGetPassword()
	{
		$url = new Url('http://user:password@domain.com/path');
		$this->assertEquals('password', $url->GetPassword());
	}
	
	public function testSetPath()
	{
		$url = new Url('http://domain.com/dir1/dir2');
		$url->SetPath('/dir3/dir4');
		$this->assertEquals('http://domain.com/dir3/dir4', $url->ToString());
	}
	
	public function testGetPath()
	{
		$url = new Url('http://domain.com/dir1/dir2');
		$this->assertEquals('/dir1/dir2', $url->GetPath());
	}
	
	public function testSetEmptyPath()
	{
		$url = new Url('http://domain.com/path');
		$url->SetPath('');
		$this->assertEquals('http://domain.com', $url->ToString());
	}
	
	public function testGetEmptyPath()
	{
		$url = new Url('http://domain.com?query');
		$this->assertEquals('', $url->GetPath());
	}
	
	public function testSetQuery()
	{
		$url = new Url('http://domain.com/path?param1=value1');
		$query = new Query('param2=value2');
		$url->SetQuery($query);
		$this->assertEquals('http://domain.com/path?param2=value2', $url->ToString());
	}
	
	public function testGetQuery()
	{
		$url = new Url('http://domain.com/path?param1=value1');
		$query = $url->GetQuery();
		$this->assertEquals('param1=value1', $query->ToString());
	}

	public function testSetFragment()
	{
		$url = new Url('http://domain.com#fragment1');
		$url->SetFragment('fragment2');
		$this->assertEquals('http://domain.com#fragment2', $url->ToString());
	}
	
	public function testGetFragment()
	{
		$url = new Url('http://domain.com#fragment1');
		$this->assertEquals('fragment1', $url->GetFragment());
	}
	
	public function testZeroSupport()
	{
		$url = new Url('http://0/0?0#0');
		$url->SetScheme('ftp');
		$this->assertEquals('ftp://0/0?0#0', $url->ToString());
	}
}

