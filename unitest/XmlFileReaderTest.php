<?php
/**
* 定义进行 XmlFileReader 单测的类
* @author zangzhe
*/

require_once dirname(__FILE__) . '/../lib/XmlFileReader.php';
require_once dirname(__FILE__) . '/../lib/utils.php';

/**
* 进行 XmlFileReader 单测的类
*/
class XmlFileReaderTest extends PHPUnit_Framework_TestCase {

    /**
     * 读取和解析文件的对象
     * @var object
    */
    protected $reader;

    /**
     * 单元测试初始化处理 
    */
    protected function setUp() {
        $this->reader = new XmlFileReader();
    }

    /**
     * XmlFileReader 成员函数 readFile 单元测试 
    */
    public function testReadFile() {
        $noExistFile = './resource/noexistfile.xml';
        $goodFile    = './resource/file1.xml';
        $goodGbkFile = './resource/file1.gbk.xml';
        $outputRegex = ".*not exists\n";

        $ret = $this->reader->readFile($goodFile);
        $this->assertStringEqualsFile($goodFile, $ret);
        
        $ret = $this->reader->readFile($goodGbkFile, 'gbk');
        $this->assertTrue(is_string($ret));

        $this->expectOutputRegex('/' . $outputRegex . '/');
        $ret = $this->reader->readFile($noExistFile);
        $this->assertNull($ret);
        
    }

    /**
     * XmlFileReader 成员函数 flushDiffToFile 单元测试 
    */
    public function testFlushDiffToFile() {
        $goodOutFile    = './resource/goodOutFile';
        $noWriteOutFile = './resource/noWriteOutFile';
        $outputRegex    = ".*failed to open stream: Permission denied\n";

        $this->expectOutputRegex('/' . $outputRegex . '/');
        $ret = $this->reader->flushDiffToFile($goodOutFile);
        $this->assertEquals(0, $ret);

        $ret = $this->reader->flushDiffToFile($noWriteOutFile);
        $this->assertEquals(0, $ret);

    }
  
    /**
     * XmlFileReader 成员函数 loadFiles 单元测试 
    */
    public function testLoadFiles() {
        $noExistFile      = './resource/noExistFile';
        $goodLeftFile     = './resource/file1.xml';
        $goodRightFile    = './resource/file2.xml';
        $goodLeftGbkFile  = './resource/file1.gbk.xml';
        $goodRightGbkFile = './resource/file2.gbk.xml';
        $outputRegex    = ".*not exists\n.*";

        $this->expectOutputRegex('/' . $outputRegex . '/');

        $ret = $this->reader->loadFiles(array($noExistFile, $noExistFile));
        $this->assertEquals(-1, $ret);

        $ret = $this->reader->loadFiles(array($goodLeftFile, $goodRightFile));
        $this->assertEquals(0, $ret);

        $ret = $this->reader->loadFiles(array($goodLeftGbkFile, $goodRightGbkFile), 'gbk');
        $this->assertEquals(0, $ret);            
        
    }

    /**
     * XmlFileReader 成员函数 getNextPairs 单元测试 
    */
    public function testGetNextGroup() {        

        $goodLeftFile   = './resource/file1.xml';
        $goodRightFile  = './resource/file2.xml';
        $this->reader->loadFiles(array($goodLeftFile, $goodRightFile));
        $ret = $this->reader->getNextGroup();
        $this->assertTrue(is_array($ret));
    }

    /**
     * XmlFileReader 成员函数 storeCurrentDiff 单元测试 
    */
    public function testStoreCurrentDiff() {
        $ret = $this->reader->storeCurrentDiff('diff contents');
        $this->assertEquals(0, $ret);
    }

}

?>
