<?php
namespace Tests\Url;

use Rost\Url\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$query = new Query();
		$this->assertEquals('', $query);
		$query = new Query('');
		$this->assertEquals('', $query);
		$query = new Query('0');
		$this->assertEquals('0', $query->ToString());
		$query = new Query('p1=v1&p2=v2');
		$this->assertEquals('p1=v1&p2=v2', $query);
	}
	
	public function testSetParameter()
	{
		$query = new Query('p1=v1');
		
		$query->SetParameter('p2', 'v2');
		$this->assertEquals('p1=v1&p2=v2', $query);
		
		$query->SetParameter('p3', 'v3');
		$this->assertEquals('p1=v1&p2=v2&p3=v3', $query);
		
		$query->SetParameter('p1', 'new');
		$this->assertEquals('p1=new&p2=v2&p3=v3', $query);
	}
	
	public function testHasParameter()
	{
		$query = new Query('p1=v1&p2=v2');
		
		$this->assertEquals(false, $query->HasParameter('p0'));
		$this->assertEquals(true, $query->HasParameter('p1'));
		$this->assertEquals(true, $query->HasParameter('p2'));
	}
	
	public function testGetParameter()
	{
		$query = new Query('p1=v1&p2=v2');
		
		$this->assertEquals(null, $query->GetParameter('p0'));
		$this->assertEquals('default', $query->GetParameter('p0', 'default'));
		$this->assertEquals('v1', $query->GetParameter('p1'));
		$this->assertEquals('v2', $query->GetParameter('p2'));
	}
	
	public function testRemoveParameter()
	{
		$query = new Query('p1=v1&p2=v2');
		
		$query->RemoveParameter('p0');
		$this->assertEquals('p1=v1&p2=v2', $query);
		
		$query->RemoveParameter('p1');
		$this->assertEquals('p2=v2', $query);
		
		$query->RemoveParameter('p2');
		$this->assertEquals('', $query);
	}
	
	public function testIsModified()
	{
		$query = new Query('p1=v1&p2=v2');
		$this->assertEquals(false, $query->IsModified());
		
		$query = new Query('p1=v1&p2=v2');
		$query->RemoveParameter('p1');
		$this->assertEquals(true, $query->IsModified());
		
		$query = new Query('p1=v1&p2=v2');
		$query->SetParameter('p1', 'new');
		$this->assertEquals(true, $query->IsModified());
	}
	
	public function testUrlEncoding()
	{
		$query = new Query('p=v+v%26v');
		$value = $query->GetParameter('p');
		$this->assertEquals('v v&v', $value);
		
		$query = new Query('');
		$query->SetParameter('p', 'v v&v');
		$this->assertEquals('p=v+v%26v', $query);
	}
}

