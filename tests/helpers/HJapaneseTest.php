<?php
use batsg\helpers\HJapanese;
use batsg\helpers\HDateTime;

class HDateTimeTest extends PHPUnit_Framework_TestCase 
{
	private $hJapanese;

	public function setUp()
	{
		$this->hJapanese = new hJapanese();
	}

	public function testReplaceFullWidthDigits()
	{
		$this->assertEquals('0123456789', $this->hJapanese->replaceFullWidthDigits('０１２３４５６７８９'));
	}

	public function testReplaceHalfWidthDigits()
	{
		$this->assertEquals('０１２３４５６７８９', $this->hJapanese->replaceHalfWidthDigits('0123456789'));
	}

	public function testParseDateTime()
	{
		$this->assertEquals("1993-11-03 02:34:12", $this->hJapanese->parseDateTime("1993年11月3日2時34分12秒"));		
	}

	public function testSjisToUtf8()
	{

		$filename = "japanese.txt";
		if (file_exists($filename) && is_readable($filename))
		{
			$content = file_get_contents($filename, 'r');
			$this->assertEquals(mb_convert_encoding('こんにちは', 'UTF-8', 'SJIS'), $this->hJapanese->sjisToUtf8($content));
		}
	}

	public function testUtf8ToSjis()
	{	
		$filename = "japanese.txt";
		if (file_exists($filename) && is_readable($filename))
		{
			$content = file_get_contents($filename, 'r');
			$this->assertEquals(mb_convert_encoding('こんにちは', 'SJIS', 'UTF-8'), $this->hJapanese->Utf8ToSjis($content));
		}
	}
	/**
	* 
	*/
	public function testGetJapaneseYear()
	{
		$era = "";
		$yearNumber = 0;
		$timestamp = strtotime("2017/03/06 12:09:34");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals("平成29年", $this->hJapanese->getJapaneseYear($datetime, $era, $yearNumber));

		$timestamp = strtotime("1960/03/06 12:09:34");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals("昭和35年", $this->hJapanese->getJapaneseYear($datetime, $era, $yearNumber));

		$timestamp = strtotime("1920/03/06 12:09:34");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals("大正9年", $this->hJapanese->getJapaneseYear($datetime, $era, $yearNumber));

		$timestamp = strtotime("1902/03/06 12:09:34");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals("明治-67年", $this->hJapanese->getJapaneseYear($datetime, $era, $yearNumber));

	}


	public function testToJapaneseCalendar()
	{
		$timestamp = strtotime("2017/03/06 12:09:34");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals("平成29年03月06日", $this->hJapanese->toJapaneseCalendar($datetime, "m月d日"));

		$timestamp = strtotime("1960/03/06 12:09:34");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals("昭和35年03月06日", $this->hJapanese->toJapaneseCalendar($datetime, "m月d日"));

		$timestamp = strtotime("1920/03/06 12:09:34");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals("大正9年03月06日", $this->hJapanese->toJapaneseCalendar($datetime, "m月d日"));

		$timestamp = strtotime("1902/03/06 12:09:34");
		$datetime = new HDateTime($timestamp);
		$this->assertEquals("明治-67年03月06日", $this->hJapanese->toJapaneseCalendar($datetime, "m月d日"));
	}

	public function testMb_str_split()
	{
		$test_array1 = ['t','e','s','t'];
		$test_array2 = ['te', 'st'];
		$test_array3 = [''];
		$this->assertEquals($test_array1, $this->hJapanese->mb_str_split("test", 1));
		$this->assertEquals($test_array2, $this->hJapanese->mb_str_split("test", 2));
		$this->assertFalse(FALSE, $this->hJapanese->mb_str_split("", 0));
		$this->assertEmpty($this->hJapanese->mb_str_split("", 1000000000000));
	}

	public function testMb_str_replace()
	{
		// var_dump($this->hJapanese->mb_str_replace("", "543523hdsagfhFDJHFJKDAH天気", ""));
		$this->assertEmpty($this->hJapanese->mb_str_replace("", "", ""));		
		$this->assertEmpty($this->hJapanese->mb_str_replace("543523hdsagfhFDJHFJKDAH天気", "", ""));
		$this->assertEmpty($this->hJapanese->mb_str_replace("", "543523hdsagfhFDJHFJKDAH天気", ""));
		$this->assertEquals("ths s testng", $this->hJapanese->mb_str_replace("i", "", "this is testing"));

		$this->assertEquals("これは私のテストカスです。", $this->hJapanese->mb_str_replace("僕", "私", "これは僕のテストカスです。"));
	}

	
	public function testContainHiragana()
	{
		$this->assertEquals(1, $this->hJapanese->containHiragana("ひらがなカタカナ漢字"));
		$this->assertEquals(1, $this->hJapanese->containHiragana("ひらがな"));
		$this->assertEquals(0, $this->hJapanese->containHiragana("カタカナ"));
		$this->assertEquals(0, $this->hJapanese->containHiragana("漢字"));
	}

	public function testContainKatakana()
	{
		$this->assertEquals(1, $this->hJapanese->containKatakana("ひらがなカタカナ"));
		$this->assertEquals(1, $this->hJapanese->containKatakana("カタカナ"));
		$this->assertEquals(0, $this->hJapanese->containKatakana("漢字"));
		$this->assertEquals(0, $this->hJapanese->containKatakana("ひらがな"));
	}

	public function testOnlyHiragana()
	{
		$this->assertEquals(1, $this->hJapanese->onlyHiragana("ひらがな"));
		$this->assertEquals(0, $this->hJapanese->onlyHiragana("ひらがなカタカナ"));
		$this->assertEquals(0, $this->hJapanese->onlyHiragana("カタカナ漢字"));
		$this->assertEquals(0, $this->hJapanese->onlyHiragana("ひらがなカタカナ漢字"));
	}

	public function testOnlyKatakana()
	{
		$this->assertEquals(1, $this->hJapanese->onlyKatakana("カタカナ"));
		$this->assertEquals(0, $this->hJapanese->onlyKatakana("ひらがなカタカナ"));
		$this->assertEquals(0, $this->hJapanese->onlyKatakana("カタカナ漢字"));
		$this->assertEquals(0, $this->hJapanese->onlyKatakana("ひらがなカタカナ漢字"));
	}

	
	public function testHiraganaToKatakana()
	{
		$this->assertEquals("ヒラガナ", $this->hJapanese->hiraganaToKatakana("ひらがな"));
		$this->assertEquals("ヒラガナ", $this->hJapanese->hiraganaToKatakana("ひらガナ"));
	}

	public function testKatakanaToHiragana()
	{
		$this->assertEquals("ひらがな", $this->hJapanese->katakanaToHiragana("ヒラガナ"));
		$this->assertEquals("ひらがな", $this->hJapanese->katakanaToHiragana("ひらガナ"));
	}

	public function testStrlen()
	{
		$this->assertEquals(7, $this->hJapanese->strlen("testing"));
		$this->assertEquals(9, $this->hJapanese->strlen("これは日本語です。"));
		$this->assertEmpty($this->hJapanese->strlen(""));
	}

}