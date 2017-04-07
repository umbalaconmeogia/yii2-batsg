<?php
use batsg\helpers\HFile;

class HDateTimeTest extends PHPUnit_Framework_TestCase 
{
	private $hFile;
	public function setUp()
	{
		$this->hFile = new HFile();
	}
	public function testConnectPath()
	{
		$this->assertEquals("C:\Users\KhoaThien\Google Drive", $this->hFile->connectPath("C:", "Users", "KhoaThien", "Google Drive"));

		$this->assertEmpty($this->hFile->connectPath());
	}

	public function testListFileRecursively()
	{
		var_dump($this->hFile->listFileRecursively("C:\data\projects.it\yii2-batsg\\test_directory\subdir"));
	}
}
