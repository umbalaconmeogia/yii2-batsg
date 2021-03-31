<?php
use batsg\helpers\HDateTime;

class HDateTime2Test extends PHPUnit_Framework_TestCase {

	private $lastMomentOf2017;
	private $lastDayOf2017;

	private $firstMomentOf2018;
	private $firstDayOf2018;

	private $lastMomentOfFeburary;
	private $lastDayOfFaburary;

	private $firstMomentOfMarch;
	private $firstDayOfMarch;

	public function setUp()
	{
		$this->lastMomentOf2017 = strtotime("2017/12/31 23:59:59");
		$this->lastDayOf2017 = new HDateTime($this->lastMomentOf2017);

		$this->firstMomentOf2018 = strtotime("2018/1/1 00:00:01");
		$this->firstDayOf2018 = new HDateTime($this->firstMomentOf2018);

		$this->lastMomentOfFeburary = strtotime("2017/2/28 23:59:59");
		$this->lastDayOfFaburary = new HDateTime($this->lastMomentOfFeburary);

		$this->firstMomentOfMarch = strtotime("2017/3/1 00:00:01");
		$this->firstDayOfMarch = new HDateTime($this->firstMomentOfMarch);
	}

	public function testReset()
	{
		$this->assertNotNull($this->lastDayOf2017->reset(2017, 12, 31, 23, 59, 59));
		$this->assertNotNull($this->firstDayOf2018->reset(2018, 01, 01, 00, 59, 59));
	}

	public function testGetYear()
	{	
		$this->assertEquals(2017, $this->lastDayOf2017->getYear());
		$this->assertEquals(2018, $this->firstDayOf2018->getYear());
	}

	public function testGetMonth()
	{	
		$this->assertEquals(1, $this->firstDayOf2018->getMonth());
		$this->assertEquals(2, $this->lastDayOfFaburary->getMonth());
		$this->assertEquals(3, $this->firstDayOfMarch->getMonth());
		$this->assertEquals(12, $this->lastDayOf2017->getMonth());
	}

	public function testGetDay()
	{
		$this->assertEquals(1, $this->firstDayOf2018->getDay());
		$this->assertEquals(28, $this->lastDayOfFaburary->getDay());
		$this->assertEquals(31, $this->lastDayOf2017->getDay());
	}

	
}
?>