<?php
class DataTest extends PHPUnit_Framework_TestCase 
{
	/**
	*@dataProvider providerMethod
	*/
	public function testAdd($a, $b, $sum)
	{
		$this->assertEquals($sum, $a + $b);
	}

	public function providerMethod()
	{
		return array(
			array(1, 1, 2),
			array(0, 1, 1),
			array(0, 0, 0)
		);
	}
}
?>