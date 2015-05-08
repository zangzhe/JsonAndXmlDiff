<?php
/**
* 定义进行 JsonFileReader 单测的类
* @author zangzhe
*/

$JsonFileReaderTestDir = dirname(__FILE__);

require_once $JsonFileReaderTestDir . '/../lib/JsonFileReader.php';
require_once $JsonFileReaderTestDir . '/../lib/utils.php';

/**
* 进行 JsonFileReader 单测的类
*/
class JsonFileReaderTest extends PHPUnit_Framework_TestCase {

    /**
     * 读取和解析文件的对象
     * @var object
    */
    protected $reader;

    /**
     * 单元测试初始化处理 
    */
    protected function setUp() {
        $this->reader = new JsonFileReader();
    }

    /**
     * JsonFileReader 成员函数 readFile 单元测试 
     * @condition case 1: 输入正常的 utf-8 编码文件
     * @expect    case 1: 读取内容和文件内容相等
     * @condition case 2: 输入正常的 gbk 编码文件
     * @expect    case 2: 读取内容和文件内容相等
     * @condition case 3: 输入异常的（不存在） utf-8 编码文件
     * @expect    case 3: 异常输出相匹配，并返回为空
    */
    public function testReadFile() {
        $noExistFile = './resource/noexistfile.json';
        $goodFile    = './resource/file1.json';
        $goodGbkFile = './resource/file1.gbk.json';
        $outputRegex = ".*not exists\n";
        // case 1
        $ret = $this->reader->readFile($goodFile);
        $this->assertStringEqualsFile($goodFile, $ret, 'good utf-8 file read fail');
        // case 2
        $ret = $this->reader->readFile($goodGbkFile, 'gbk');
        $this->assertTrue(is_string($ret), 'good gbk file read fail');
        // case 3
        $this->expectOutputRegex('/' . $outputRegex . '/');
        $ret = $this->reader->readFile($noExistFile);
        $this->assertNull($ret, 'reading bad file not return NULL');
        
    }

    /**
     * JsonFileReader 成员函数 flushDiffToFile 单元测试 
     * @condition case 1: 传入正常的输出文件
     * @expect    case 1: 返回值为 0 
     * @condition case 2: 传入异常的（无写权限）输出文件
     * @expect    case 2: 异常输出相匹配，返回值为 0
    */
    public function testFlushDiffToFile() {
        $goodOutFile    = './resource/goodOutFile';
        $noWriteOutFile = './resource/noWriteOutFile';
        $outputRegex    = ".*failed to open stream: Permission denied\n";
        // case 1
        //$this->expectOutputRegex('/' . $outputRegex . '/');
        $ret = $this->reader->flushDiffToFile($goodOutFile);
        $this->assertEquals(0, $ret, 'flush good outfile return not 0');
        // case 2
        $ret = $this->reader->flushDiffToFile($noWriteOutFile);
        $this->assertEquals(0, $ret, 'flush bad outfile return not 0');

    }
  
    /**
     * JsonFileReader 成员函数 loadFiles 单元测试 
     * @condition case 1: 传入异常的（不存在）对比文件数组
     * @expect    case 1: 返回值为 -1 
     * @condition case 2: 传入正常的 utf-8 对比文件数组
     * @expect    case 2: 返回值为 0
     * @condition case 3: 传入正常的 gbk 对比文件数组
     * @expect    case 3: 返回值为 0
    */
    public function testLoadFiles() {
        $noExistFile      = './resource/noExistFile';
        $goodLeftFile     = './resource/file1.json';
        $goodRightFile    = './resource/file2.json';
        $goodLeftGbkFile  = './resource/file1.gbk.json';
        $goodRightGbkFile = './resource/file2.gbk.json';
        $outputRegex    = ".*not exists\n.*";

        $this->expectOutputRegex('/' . $outputRegex . '/');
        // case 1
        $ret = $this->reader->loadFiles(array($noExistFile, $noExistFile));
        $this->assertEquals(-1, $ret, 'load bad file not return -1');
        // case 2
        $ret = $this->reader->loadFiles(array($goodLeftFile, $goodRightFile));
        $this->assertEquals(0, $ret, 'load good utf-8 file not return 0');
        // case 3
        $ret = $this->reader->loadFiles(array($goodLeftGbkFile, $goodRightGbkFile), 'gbk');
        $this->assertEquals(0, $ret, 'load good gbk file not return 0');            
        
    }

    /**
     * JsonFileReader 成员函数 getNextPairs 单元测试 
     * @condition 传入正常的对比文件数组
     * @expect 返回值为数组对象
    */
    public function testGetNextGroup() {    

        $goodLeftFile   = './resource/file1.json';
        $goodRightFile  = './resource/file2.json';
        $this->reader->loadFiles(array($goodLeftFile, $goodRightFile));
        $ret = $this->reader->getNextGroup();
        $this->assertTrue(is_array($ret), 'getNextGroup not return an array obj');
    }

    /**
     * JsonFileReader 成员函数 storeCurrentDiff 单元测试 
     * @condition 传入字符串
     * @expect 返回值为 0
    */
    public function testStoreCurrentDiff() {
        $ret = $this->reader->storeCurrentDiff('diff contents');
        $this->assertEquals(0, $ret, 'store CurrentDiff not return 0');
    }

}

?>
