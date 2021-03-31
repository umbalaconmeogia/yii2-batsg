<?php
use batsg\helpers\HFile;


class HDateTimeTest extends PHPUnit_Framework_TestCase 
{
	protected $hFile;

	public function setUp()
	{
		$this->hFile = new HFile();

		if(!file_exists("test_directory/subdir3/subdir4/subdir5"))
		{
			mkdir("test_directory/subdir3/subdir4/subdir5");
		}

		if(!file_exists("test_directory/subdir3/subdir4/subdir5/subdir6"))
		{
			mkdir("test_directory/subdir3/subdir4/subdir5/subdir6");
		}

	}
	public function testConnectPath()
	{
		$this->assertEquals("test_directory\subdir", $this->hFile->connectPath("test_directory", "subdir"));

		$this->assertEmpty($this->hFile->connectPath());
	}

	public function testListFileRecursively()
	{
		$expectedArray = array(
			'test_directory/filetest3',
			'test_directory/subdir/filetest1.txt',
			'test_directory/subdir/filetest2.doc');
		$this->assertEquals($expectedArray, $this->hFile->listFileRecursively("test_directory"));
	}

	public function testListFile()
	{
		$expectedArray = array(
			"filetest1.txt" => "test_directory/subdir/filetest1.txt",
			"filetest2.doc" => "test_directory/subdir/filetest2.doc"
		);
		$this->assertEquals($expectedArray, $this->hFile->listFile("test_directory/subdir"));
	}

	public function testListDir()
	{
		$this->assertEmpty($this->hFile->listDir("test_directory/subdir2"));
		$expectedArray = array(
			"subdir" => "test_directory/subdir",
			"subdir2" => "test_directory/subdir2",
			"subdir3" => "test_directory/subdir3",

		);
		$this->assertEquals($expectedArray, $this->hFile->listDir("test_directory"));
	}

	public function testFileExtension()
	{
		$this->assertNull($this->hFile->fileExtension("test_directory/filename"));
		$this->assertEquals("txt", $this->hFile->fileExtension("test_directory/subdir/filetest1.txt"));
		$this->assertEquals("doc", $this->hFile->fileExtension("test_directory/subdir/filetest2.doc"));
	}

	public function testFileFileName()
	{
		$this->assertEquals("filename3", $this->hFile->fileFileName("test_directory/filename3"));
		$this->assertEquals("filetest1", $this->hFile->fileFileName("test_directory/subdir/filetest1.txt"));
		$this->assertEquals("filetest2", $this->hFile->fileFileName("test_directory/subdir/filetest2.doc"));
	}
	public function testRmdir(){
		$this->assertNull($this->hFile->rmdir("test_directory/subdir3/subdir4/subdir5/subdir6"));
	}
}
