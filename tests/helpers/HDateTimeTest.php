<?php
use batsg\helpers\HDateTime;

class HDateTimeTest extends PHPUnit_Framework_TestCase {

	public function testConstructor()
	{
		$timestamp = time();
		$datetime = new HDateTime($timestamp);
		$this->assertNotNull($datetime);
	}


	public function testResetByTimestamp()
	{
		$timestamp = time();
		$datetime = new HDateTime($timestamp);
		$this->assertNotNull($datetime->resetByTimestamp($timestamp));
	}

	public function testReset()
	{
		$timestamp = time();
		$datetime = new HDateTime($timestamp);
		$this->assertNotNull($datetime->reset(2017, 02, 19, 12, 34, 34));
	}


	public function testGetYear()
	{
		$timestamp = strtotime("2017/4/4 7:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(2017, $datetime->getYear());
		
		$timestamp = strtotime("1972/4/4 7:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(1972, $datetime->getYear());
	}

	public function testGetMonth()
	{
		$timestamp = strtotime("2017/12/4 7:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(12, $datetime->getMonth());

		$timestamp = strtotime("2017/10/4 7:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(10, $datetime->getMonth());

		$timestamp = strtotime("2010/1/4 7:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(1, $datetime->getMonth());
	}

	public function testGetDay()
	{
		$timestamp = strtotime("2017/3/31 7:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(31, $datetime->getDay());
		
		$timestamp = strtotime("1960/4/1 7:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(1, $datetime->getDay());

		$timestamp = strtotime("1960/4/02 7:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(2, $datetime->getDay());
	}

	public function testGetHour()
	{
		$timestamp = strtotime("2017/3/31 01:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(1, $datetime->getHour());
		
		$timestamp = strtotime("1960/4/1 23:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(23, $datetime->getHour());

		$timestamp = strtotime("1960/4/02 12:40:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(12, $datetime->getHour());
	}

	public function testGetMinute()
	{
		$timestamp = strtotime("2017/3/31 01:00:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(00, $datetime->getMinute());
		
		$timestamp = strtotime("1960/4/1 23:59:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(59, $datetime->getMinute());

		$timestamp = strtotime("1960/4/02 12:21:32");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(21, $datetime->getMinute());
	}

	public function testGetSecond()
	{
		$timestamp = strtotime("2017/3/31 01:00:30");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(30, $datetime->getSecond());
		
		$timestamp = strtotime("1960/4/1 23:59:00");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(00, $datetime->getSecond());

		$timestamp = strtotime("1960/4/02 12:21:59");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals(59, $datetime->getSecond());
	}

	/**
   * Get week day value
   * int from 0 (Sunday) to 6 (Saturday).
   */
	public function testGetWDay()
	{

		// 火曜日
		$timestamp = strtotime("2017/4/4 23:59:00");
		$datetime = new HDateTime($timestamp);
		$weekday = date('N', $timestamp);
		$this->assertEquals(2, $datetime->getWDay());

		// 水曜日
		$timestamp = strtotime("2017/4/5 23:59:00");
		$datetime = new HDateTime($timestamp);
		$weekday = date('N', $timestamp);
		$this->assertEquals(3, $datetime->getWDay());

		// 木曜日
		$timestamp = strtotime("2017/4/6 23:59:00");
		$datetime = new HDateTime($timestamp);
		$weekday = date('N', $timestamp);
		$this->assertEquals(4, $datetime->getWDay());


		// 金曜日
		$timestamp = strtotime("2017/4/7 23:59:00");
		$datetime = new HDateTime($timestamp);
		$weekday = date('N', $timestamp);
		$this->assertEquals(5, $datetime->getWDay());

		// 土曜日
		$timestamp = strtotime("2017/4/8 23:59:00");
		$datetime = new HDateTime($timestamp);
		$weekday = date('N', $timestamp);
		$this->assertEquals(6, $datetime->getWDay());

		// 日曜日
		$timestamp = strtotime("2017/4/9 23:59:00");
		$datetime = new HDateTime($timestamp);
		$weekday = date('N', $timestamp);
		$this->assertEquals(0, $datetime->getWDay());

		//月曜日
		$timestamp = strtotime("2017/4/10 23:59:00");
		$datetime = new HDateTime($timestamp);
		$weekday = date('N', $timestamp);
		$this->assertEquals(1, $datetime->getWDay());

	}

	public function testGetTimestamp()
	{
		$timestamp = strtotime("2017/4/4 23:59:00");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals($timestamp, $datetime->getTimestamp());
	}

	// 
	public function test__ToString()
	{
		$timestamp = strtotime("2017/4/4 23:59:00");
		$datetime = new HDateTime($timestamp);

		$timezone = 9;
		$expectedString = gmdate('Y-m-d H:i:s', $timestamp + 3600*($timezone+date("I")));
		$this->assertEquals('2017-04-04 23:59:00', $datetime->__toString());
		// $this->assertEquals($expectedString, $datetime->__toString()); 

	}

	public function testToDateStr()
	{
		$timestamp = strtotime("2017/4/4");
		$datetime = new HDateTime($timestamp);

		$timezone = 9;
		$expectedString = gmdate('Y-m-d', $timestamp + 3600*($timezone+date("I")));
		$this->assertEquals($expectedString, $datetime->toDateStr());
	}

	public function testToDateTimeStr()
	{
		$timestamp = strtotime("2017/4/4 23:59:00");
		$datetime = new HDateTime($timestamp);

		$timezone = 9;
		$expectedString = gmdate('Y-m-d H:i:s', $timestamp + 3600*($timezone+date("I")));
		$this->assertEquals($expectedString, $datetime->toDateTimeStr());
	}		
	

	public function testToTimeStr()
	{
		$timestamp = strtotime("23:59:00");
		$datetime = new HDateTime($timestamp);

		$timezone = 9;
		$expectedString = gmdate('H:i:s', $timestamp + 3600*($timezone+date("I")));
		$this->assertEquals($expectedString, $datetime->toTimeStr());
	}

	public function testToString()
	{

		$timestamp = strtotime("2017/4/4 23:59:00");
		$datetime = new HDateTime($timestamp);

		$timezone = 9;
		$expectedString = gmdate('Y-m-d H:i:s', $timestamp + 3600*($timezone+date("I")));
		$this->assertEquals($expectedString, $datetime->toString()); 
	}

	public function testFirstDayOfMonth()
	{
		$timestamp1 = strtotime("2017/4/4 23:59:00");
		$datetime1 = new HDateTime($timestamp1);
		// echo $datetime->firstDayOfMonth();
		$timestamp2 = strtotime("2017/4/1 00:00:00");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->firstDayOfMonth());
	}

	public function testLastDayOfMonth()
	{
		$timestamp = strtotime("2017/4/4 23:59:00");
		$datetime1 = new HDateTime($timestamp);
		$datetime2 = date('Y-m-t 00:00:00', $timestamp);
		$this->assertEquals($datetime2, $datetime1->lastDayOfMonth());

		$timestamp = strtotime("2017/3/4 23:59:00");
		$datetime1 = new HDateTime($timestamp);
		$datetime2 = date('Y-m-t 00:00:00', $timestamp);
		$this->assertEquals($datetime2, $datetime1->lastDayOfMonth());

		$timestamp = strtotime("2017/2/4 23:59:00");
		$datetime1 = new HDateTime($timestamp);
		$datetime2 = date('Y-m-t 00:00:00', $timestamp);
		$this->assertEquals($datetime2, $datetime1->lastDayOfMonth());
	}

	public function testDate()
	{
		$timestamp = strtotime("2017/4/4");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals('2017-04-04 00:00:00', $datetime->date());
	}

	public function testAdd()
	{
		// case 1: 2017/12/31 23:59:59
		$timestamp1 = strtotime("2017/12/31 23:59:59");
		$datetime1 = new HDateTime($timestamp1);

		// +2 seconds
		$timestamp2 = strtotime('2018-1-1 00:00:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,0,0,2));
		
		// +2 minutes
		$timestamp2 = strtotime('2018-1-1 00:01:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,0,2,0));
		
		//  +2 hours
		$timestamp2 = strtotime('2018-1-1 1:59:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,2,0,0));

		// +2 days
		$timestamp2 = strtotime('2018-1-2 23:59:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,2,0,0,0));

		// +2 months
		$timestamp2 = strtotime('2018-3-3 23:59:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,2,0,0,0,0));

		// +2 years
		$timestamp2 = strtotime('2019-12-31 23:59:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(2,0,0,0,0,0));

// -----------------------------------------------------------------------------
		// case 2: 2018/1/1 00:00:01
		$timestamp1 = strtotime("2018/1/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);

		// -2 seconds
		$timestamp2 = strtotime('2017-12-31 23:59:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,0,0,-2));
		
		// -2 minutes
		$timestamp2 = strtotime('2017-12-31 23:58:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,0,-2,0));
		

		// -2 hours
		$timestamp2 = strtotime('2017-12-31 22:00:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,-2,0,0));
		
		// -2 days
		$timestamp2 = strtotime('2017-12-30 00:00:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,-2,0,0,0));
		
		// -2 months
		$timestamp2 = strtotime('2017-11-01 00:00:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,-2,0,0,0,0));
		
		// -2 years
		$timestamp2 = strtotime('2016-01-01 00:00:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(-2,0,0,0,0,0));
		
// ---------------------------------------------------------------------------

		// case 3: 2017/2/28 23:59:59
		$timestamp1 = strtotime("2017/2/28 23:59:59");
		$datetime1 = new HDateTime($timestamp1);
		
		// +2 seconds
		$timestamp2 = strtotime('2017-3-1 00:00:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,0,0,2));

		// +2 minutes
		$timestamp2 = strtotime('2017-3-1 00:01:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,0,2,0));

		// +2 hours
		$timestamp2 = strtotime('2017-3-1 1:59:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,2,0,0));

		// +2 days
		$timestamp2 = strtotime('2017-3-2 23:59:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,2,0,0,0));
// --------------------------------------------------------------------------
		// case 3: 2017/3/1 00:00:01
		$timestamp1 = strtotime("2017/3/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);

		// -2 seconds
		$timestamp2 = strtotime('2017-2-28 23:59:59');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,0,0,-2));
		
		// -2 minutes
		$timestamp2 = strtotime('2017-2-28 23:58:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,0,-2,0));
		
		// -2 hours
		$timestamp2 = strtotime('2017-2-28 22:00:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,0,-2,0,0));

			
		// -2 days
		$timestamp2 = strtotime('2017-2-27 00:00:01');
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->add(0,0,-2,0,0,0));
	}

	public function testNextNYear()
	{
		$timestamp = strtotime("2017/1/4");
		$datetime = new HDateTime($timestamp);
		
		$this->assertEquals(2020, $datetime->nextNYear(3)->getYear());
		$this->assertEquals(2014, $datetime->nextNYear(-3)->getYear());
				
	}

	public function testNextNMonth()
	{
		$timestamp = strtotime("2017/1/4 23:59:00");
		$datetime = new HDateTime($timestamp);
		$nextNMonth = $datetime->nextNMonth(-2);
		$this->assertEquals(11, $nextNMonth->getMonth());
		$this->assertEquals(2016, $nextNMonth->getYear());

		$timestamp = strtotime("2017/11/4 23:59:00");
		$datetime = new HDateTime($timestamp);
		$nextNMonth = $datetime->nextNMonth(2);
		$this->assertEquals(1, $nextNMonth->getMonth());
		$this->assertEquals(2018, $nextNMonth->getYear());


		$timestamp = strtotime("2017/7/4 23:59:00");
		$datetime = new HDateTime($timestamp);
		$nextNMonth = $datetime->nextNMonth(3);
		$this->assertEquals(10, $nextNMonth->getMonth());
	

		$timestamp = strtotime("2017/7/4 23:59:00");
		$datetime = new HDateTime($timestamp);
		$nextNMonth = $datetime->nextNMonth(-3);
		$this->assertEquals(4, $nextNMonth->getMonth());
	}

	public function testNextNDay()
	{
		// increasing 2 days of months that have 31 days 
		$timestamp = strtotime("2017/3/31 23:59:00");
		$datetime = new HDateTime($timestamp);
		$nextNDay = $datetime->nextNDay(2);
		$this->assertEquals(2, $nextNDay->getDay());
		$this->assertEquals(4, $nextNDay->getMonth());

		// increasing 4 days of months that have 28 days
		$timestamp = strtotime("2017/2/28 23:59:00");
		$datetime = new HDateTime($timestamp);
		$nextNDay = $datetime->nextNDay(4);
		$this->assertEquals(4, $nextNDay->getDay());
		$this->assertEquals(3, $nextNDay->getMonth());



		// increasing 5 days from LAST day of years
		$timestamp = strtotime("2016/12/31 23:59:00");
		$datetime = new HDateTime($timestamp);
		$nextNDay = $datetime->nextNDay(5);
		$this->assertEquals(5, $nextNDay->getDay());
		$this->assertEquals(1, $nextNDay->getMonth());
		$this->assertEquals(2017, $nextNDay->getYear());

		// decreasing 5 days from FIRST day of years.
		$timestamp = strtotime("2017/1/1 23:59:00");
		$datetime = new HDateTime($timestamp);
		$nextNDay = $datetime->nextNDay(-5);
		$this->assertEquals(27, $nextNDay->getDay());
		$this->assertEquals(12, $nextNDay->getMonth());
		$this->assertEquals(2016, $nextNDay->getYear());
	}

	public function testNextNHour()
	{
		// case 1: 2017/12/31 23:59:00
		// +4 hours
		$timestamp1 = strtotime("2017/12/31 23:59:00");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2018/1/1 03:59:00");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNHour(4));

		// case 2: 2017/2/28 23:59:59
		// +4 hours
		$timestamp1 = strtotime("2017/2/28 23:59:59");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/3/1 03:59:59");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNHour(4));

		// case 3: 2018/1/1 01:59:59
		// -4 hours
		$timestamp1 = strtotime("2018/1/1 01:59:59");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/12/31 21:59:59");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNHour(-4));

		// case 4: 2017/3/1 01:59:59
		// -4 hours
		$timestamp1 = strtotime("2017/3/1 01:59:59");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/2/28 21:59:59");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNHour(-4));

	}

	public function testNexNMinute()
	{
		// case 1: 2017/12/31 23:59:59
		// +20 minutes		
		$timestamp1 = strtotime("2017/12/31 23:59:59");
		$datetime1 = new HDateTime($timestamp1);	
		$timestamp2 = strtotime("2018/1/1 00:19:59");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNMinute(20));


		// case 2: 2017/2/28 23:59:59
		// +20 minutes
		$timestamp1 = strtotime("2017/2/28 23:59:59");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/3/1 00:19:59");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNMinute(20));

		// case 3: 2018/1/1 00:00:01
		// -20 minutes
		$timestamp1 = strtotime("2018/1/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/12/31 23:40:01");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNMinute(-20));

		// case 4: 2017/3/1 00:00:59
		// -20 minutes
		$timestamp1 = strtotime("2017/3/1 00:00:59");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/2/28 23:40:59");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNMinute(-20));
	}

	public function testNextNSecond()
	{
		// case 1: 2017/12/31 23:59:59
		// +20 seconds		
		$timestamp1 = strtotime("2017/12/31 23:59:59");
		$datetime1 = new HDateTime($timestamp1);	
		$timestamp2 = strtotime("2018/1/1 00:00:19");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNSecond(20));
 

		// case 2: 2017/2/28 23:59:59
		// +20 seconds
		$timestamp1 = strtotime("2017/2/28 23:59:59");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/3/1 00:00:19");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNSecond(20));

		// // case 3: 2018/1/1 00:00:01
		// // -20 seconds
		$timestamp1 = strtotime("2018/1/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/12/31 23:59:41");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNSecond(-20));

		// // case 4: 2017/3/1 00:00:01
		// // -20 seconds
		$timestamp1 = strtotime("2017/3/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/2/28 23:59:41");
		$datetime2 = new HDateTime($timestamp2);
		$this->assertEquals($datetime2, $datetime1->nextNSecond(-20));	
	}

	// test static 

	public function testCreateFromString()
	{
		$timestamp1 = strtotime("2017/3/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);
		$this->assertNotNull($datetime1->createFromString($datetime1));
	}

	public function testCreateFromYmdHms()
	{
		$timestamp1 = strtotime("2017/3/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);
		$this->assertEquals($datetime1, $datetime1->createFromYmdHms(2017, 3, 1, 0, 0, 1));	
	}
	public function testCreateFromTimeStamp()
	{
		$timestamp1 = strtotime("2017/3/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);
		$this->assertNotNull($datetime1->createFromTimestamp($timestamp1));
	}

	public function testNow()
	{
		$timestamp = time();
		$datetime = new HDateTime($timestamp);
		$this->assertEquals($datetime, $datetime->now());
	}

	public function testCmp()
	{
		$timestamp1 = strtotime("2018/1/1 00:00:01");
		$datetime1 = new HDateTime($timestamp1);
		$timestamp2 = strtotime("2017/12/31 23:59:41");
		$datetime2 = new HDateTime($timestamp2);

		$this->assertEquals(1,  $datetime1->cmp($datetime1, $datetime2));
		$this->assertEquals(-1, $datetime2->cmp($datetime2, $datetime1));
		$this->assertEquals(0, $datetime1->cmp(2,4));
	}	
}
?>